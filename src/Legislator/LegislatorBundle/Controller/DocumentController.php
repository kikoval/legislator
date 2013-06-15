<?php

namespace Legislator\LegislatorBundle\Controller;
use Symfony\Component\HttpFoundation\Response;

use Legislator\LegislatorBundle\Entity\Document;
use Legislator\LegislatorBundle\Entity\Comment;
use Legislator\LegislatorBundle\Form\DocumentType;
use Legislator\LegislatorBundle\Form\CommentType;
use Legislator\LegislatorBundle\Form\ContentSectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentController extends Controller {

    /**
     * View a document
     *
     * @param int $id Document ID
     * @param Request $request
     * @return Response
     */
    public function viewAction($id, Request $request)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);
        if (!$document)
            throw $this->createNotFoundException('No document found for id!');

        // TODO limit comments
        $comments = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Comment')
                ->findBy(array('document' => $document));

        // add form to add or edit a comment
        $comment_id = $request->get('comment_id');
        if ($comment_id) {
            $comment = $this->getDoctrine()
                    ->getRepository('LegislatorBundle:Comment')
                    ->find($comment_id);
            if (!$comment) {
                throw $this->createNotFoundException('No document found for id!');
            }
            $action = $this->generateUrl('legislator_comment_edit',
                    array('document_id' => $id, 'id' => $comment_id));
        } else {
            $comment = new Comment();
            $comment->setDocument($document);
            $action = $this->generateUrl('legislator_comment_new',
                        array('document_id' => $id));
        }

        $form = $this->createForm(new CommentType(), $comment, array(
                'method' => 'post',
                'action' => $action));

        // check privileges
        $user = $this->get('security.context')->getToken()->getUser();
        $is_document_owner = $user->getID() == $document->getCreatedBy()->getID();
        $can_take_comment_actions = $is_document_owner && !$document->isProcessingCommentsStatus();

        return $this->render('LegislatorBundle:Document:view.html.twig',
                array('document' => $document,
                      'comments' => $comments,
                      'form' => $form->createView(),
                      'is_document_owner' => $is_document_owner,
                      'can_take_comment_actions' => $can_take_comment_actions));
    }

    /**
     * Process delete action.
     *
     * @param int $id Document ID
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        // TODO make special role
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);
        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();

        return $this->redirect($this->generateUrl('legislator_homepage'));
    }

    /**
     * Process form for adding a document.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        // TODO make special role
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        // create form
        $document = new Document();
        $document->setVersion(1); // default value

        $form = $this->createForm(new DocumentType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $document->setStatus(Document::STATUS_NEW); // default value
            $document->setModifiedOn(new \DateTime());
            $document->setCreatedOn(new \DateTime());
            $user = $this->get('security.context')->getToken()->getUser();
            $document->setCreatedBy($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('legislator_homepage'));
        }

        // display form
        return $this->render('LegislatorBundle:Document:new.html.twig',
                array('form' => $form->createView()));
    }

    /**
     * Process edit action.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editAction(Request $request)
    {
        $id = $request->get('id');
        $document = $this->getDoctrine()
                    ->getRepository('LegislatorBundle:Document')->find($id);

        if (!$document)
            throw $this->createNotFoundException('No document found for id!');

        $form = $this->createForm(new DocumentType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $document->setModifiedOn(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('legislator_view', array('id' => $id)));
        }

        // display form
        return $this->render('LegislatorBundle:Document:edit.html.twig',
                array('form' => $form->createView(), 'id' => $id));

    }

    /**
     * Enable commenting
     *
     * @param int $id Document ID
     * @return @see setCommenting()
     */
    public function enableCommentingAction($id)
    {
        return $this->setCommenting($id, 1);
    }

    /**
     * Disable commenting
     *
     * @param int $id Document ID
     * @return @see setCommenting()
     */
    public function disableCommentingAction($id)
    {
        return $this->setCommenting($id, 0);
    }

    /**
     *
     * @param int $id
     * @param bool $value
     * @throws AccessDeniedException
     * @return RedirectResponse
     */
    private function setCommenting($id, $value)
    {
        // TODO make special role
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);

        if (!$document)
            throw $this->createNotFoundException('No document found for id!');

        $document->setCanBeCommented($value);

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        return $this->redirect($this->generateUrl('legislator_view', array('id' => $id)));
    }

    /**
     * Start the phase of processing of comments.
     *
     * @param int $id Document ID
     * @return RedirectResponse
     */
    public function processCommentsAction($id)
    {
        $document = $this->getDoctrine()
            ->getRepository('LegislatorBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        $document->setStatus(Document::STATUS_PROCESSING_COMMENTS);

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        return $this->redirect($this->generateUrl('legislator_view', array('id' => $id)));
    }

    /**
     * Start the phase of processing of comments.
     *
     * @param int $id Document ID
     * @return RedirectResponse
     */
    public function finishcommentingAction($id)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        $document->setStatus(Document::STATUS_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        // TODO notify users that commented that the commenting the document
        // is finished

        return $this->redirect($this->generateUrl('legislator_view', array('id' => $id)));
    }
}
