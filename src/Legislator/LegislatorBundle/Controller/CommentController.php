<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Legislator\LegislatorBundle\Entity\Comment;
use Legislator\LegislatorBundle\Form\CommentType;
use Legislator\LegislatorBundle\Form\CommentReplyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller {

    public function preExecute()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
    }

    /**
     * View comments by a user.
     *
     * @param int $user_id if null the current user will be used
     */
    public function viewByUserAction($user_id=null)
    {
        $view_mine = true;
        if ($user_id !== null) {
            $user = $this->getDoctrine()
                ->getRepository('LegislatorBundle:User')->find($user_id);

            if ($user == null) {
                throw $this->createNotFoundException('No user found for id!');
            }
            $view_mine = false;
        } else {
            $user = $this->getUser();
        }
        $comments = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Comment')->findBy(
                                array('createdBy' => $user),
                                array('modifiedOn' => 'DESC',
                                      'createdOn' => 'DESC'));

        if ($view_mine) {
	        return $this->render('LegislatorBundle:Comment:view_mine.html.twig',
	                array('comments' => $comments));
        } else {
            return $this->render('LegislatorBundle:Comment:view_by_user.html.twig',
                    array('comments' => $comments));
        }
    }

	/**
	 * Processes submitted form for adding new comment.
	 *
	 * @param int $id Document ID
	 * @param Request $request
	 * @throws NotFoundHttpException
	 */
	public function newAction($document_id, Request $request)
	{
		if ($document_id == null) {
			throw new NotFoundHttpException();
		}
		$document = $this->getDoctrine()
		        ->getRepository('LegislatorBundle:Document')->find($document_id);
		if ($document == null) {
			throw new $this->createNotFoundException('No document found for id!');
		}
		if (!$document->getCanBeCommented()) {
		    // TODO find a better exception
		    throw new AccessDeniedException();
		}

		$comment = new Comment();
		$comment->setDocument($document);

		$form = $this->createForm(new CommentType(), $comment);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$comment->setCreatedOn(new \Datetime());
			$user = $this->getUser();
			$comment->setCreatedBy($user);

			$em = $this->getDoctrine()->getManager();
			$em->persist($comment);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('legislator_view',
				array('id' => $document_id)));
	}

	/**
	 * Process edit form.
	 *
	 * @param int $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function editAction($document_id, $id, Request $request)
	{
	    $comment = $this->getDoctrine()
	            ->getRepository('LegislatorBundle:Comment')->find($id);

	    if ($comment == null) {
	        throw $this->createNotFoundException('No comment found for id!');
	    }

	    // check privileges, only admin or the author himself can edit a comment
	    $user = $this->getUser();
	    if ($comment->getCreatedBy()->getId() !== $user->getId()) {
	        throw new AccessDeniedException();
	    }

	    $form = $this->createForm(new CommentType(), $comment);
	    $form->handleRequest($request);

	    if ($form->isValid()) {
	        $comment->setmodifiedOn(new \Datetime());

	        $em = $this->getDoctrine()->getManager();
	        $em->persist($comment);
	        $em->flush();
	    }
	    // TODO show validation errors to the user

	    return $this->redirect($this->generateUrl('legislator_view',
	            array('id' => $comment->getDocument()->getId())));
	}

	/**
	 * Process delete action.
	 *
	 * @param int $document_id
	 * @param int $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($document_id, $id, Request $request)
	{
		$comment = $this->getDoctrine()
			->getRepository('LegislatorBundle:Comment')->find($id);
		if ($comment == null) {
			throw $this->createNotFoundException('No comment found for id!');
		}

		$doc_id = $comment->getDocument()->getId();

		$em = $this->getDoctrine()->getManager();
		$em->remove($comment);
		$em->flush();

		return $this->redirect($this->generateUrl('legislator_view',
				array('id' => $doc_id)));
	}

	/**
	 * Process reply action.
	 *
	 * @param int $document_id
	 * @param int $id
	 * @return RedirectResponse|Response
	 */
	public function replyAction($document_id, $id, Request $request)
	{
	    // check privileges: only the author of the document can reply
		$document = $this->getDoctrine()
				->getRepository('LegislatorBundle:Document')->find($document_id);
		if (!$document) {
			throw $this->createNotFoundException('No document found for id!');
		}
		$is_document_owner = $document->isOwner($this->getUser());
		if (!$is_document_owner) {
			throw new AccessDeniedException();
		}

	    $comment = $this->getDoctrine()
	        ->getRepository('LegislatorBundle:Comment')->find($id);
	    if ($comment == null) {
	        throw $this->createNotFoundException('No comment found for id!');
	    }

	    $form = $this->createForm(new CommentReplyType(), $comment);
	    $form->handleRequest($request);

	    if ($form->isValid()) {
	        $user = $this->getUser();
	        $comment->setRepliedBy($user);

	        $em = $this->getDoctrine()->getManager();
	        $em->persist($comment);
	        $em->flush();

	        return $this->redirect($this->generateUrl('legislator_view',
	                 array('id' => $comment->getDocument()->getId())));
	    }

	    // display form
	    return $this->render('LegislatorBundle:Comment:reply.html.twig',
	            array('form' => $form->createView(), 'comment' => $comment));
	}
}
