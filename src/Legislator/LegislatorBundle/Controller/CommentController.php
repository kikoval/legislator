<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Legislator\LegislatorBundle\Entity\Comment;
use Legislator\LegislatorBundle\Form\CommentType;
use Legislator\LegislatorBundle\Form\CommentReplyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller
{
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
     * @param  int                   $id      Document ID
     * @param  Request               $request
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

        // checking permissions
        if (!$document->canBeAccessed($this->getUser()) ||
                !$document->getCanBeCommented()) {
            throw new AccessDeniedException();
        }

        $comment = new Comment();
        $comment->setDocument($document);

        $form = $this->createForm(new CommentType(), $comment);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $current_datetime = new \Datetime();
            $comment->setCreatedOn($current_datetime);
            $comment->setModifiedOn($current_datetime);

            $comment->setCreatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $max_position = $em->getRepository('LegislatorBundle:Comment')
                    ->findMaxPosition($document);
            $comment->setPosition($max_position + 1);

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('legislator_document_view',
                array('id' => $document_id)));
    }

    /**
     * Process edit form.
     *
     * @param  int              $id
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function editAction($document_id, $id, Request $request)
    {
        $comment = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Comment')->find($id);

        if ($comment == null) {
            throw $this->createNotFoundException('No comment found for id!');
        }

        // check privileges, only the author himself can edit a comment
        if (!$comment->isOwner($this->getUser())) {
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
        return $this->redirect($this->generateUrl('legislator_document_view',
                array('id' => $comment->getDocument()->getId())));
    }

    /**
     * Process delete action.
     *
     * @param  int              $document_id
     * @param  int              $id
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function deleteAction($document_id, $id, Request $request)
    {
        $comment = $this->getDoctrine()
            ->getRepository('LegislatorBundle:Comment')->find($id);
        if ($comment == null) {
            throw $this->createNotFoundException('No comment found for id!');
        }

        // check privileges, only the author himself can edit a comment
        if (!$comment->isOwner($this->getUser())) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirect($this->generateUrl('legislator_document_view',
                array('id' => $document_id)));
    }

    /**
     * Process reply action.
     *
     * @param  int                       $document_id
     * @param  int                       $id
     * @return RedirectResponse|Response
     */
    public function replyAction($document_id, $id, Request $request)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($document_id);
        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        // checking ownership
        if (!$document->isOwner($this->getUser())) {
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

            return $this->redirect($this->generateUrl('legislator_document_view',
                     array('id' => $comment->getDocument()->getId())));
        }

        // display form
        return $this->render('LegislatorBundle:Comment:reply.html.twig',
                array('form' => $form->createView(), 'comment' => $comment));
    }

    /**
     * Handle AJAX call to update position of comments for a document
     *
     * @param  int     $document_id id of Document
     * @param  Request $request
     * @return Response
     */
    public function updatePositionsAction($document_id, Request $request)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($document_id);
        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }
        // checking ownership
        if (!$document->isOwner($this->getUser())) {
            throw new AccessDeniedException();
        }

        // get ordered is list of comment ids
        $order = $request->get('order');
        if (!empty($order)) {
            $order = explode(',', $order);

            if (count($order) > 1) {
                $new_position = 1;
                foreach ($order as $id) {
                    $id = intval($id);
                    $comment = $this->getDoctrine()
                        ->getRepository('LegislatorBundle:Comment')->find($id);

                    if ($comment !== NULL) {
                        $comment->setPosition($new_position);

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($comment);
                        $em->flush();
                    } else {
                        return new Response('Error', 400);
                    }
                    $new_position += 1;
                }
            }

            return new Response();
        }

        return new Response('Error', 400);
    }
}

