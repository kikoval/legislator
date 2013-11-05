<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

use Legislator\LegislatorBundle\Entity\Document;
use Legislator\LegislatorBundle\Entity\Comment;
use Legislator\LegislatorBundle\Form\DocumentType;
use Legislator\LegislatorBundle\Form\DocumentStatusType;
use Legislator\LegislatorBundle\Form\DocumentNewType;
use Legislator\LegislatorBundle\Form\DocumentNewVersionType;
use Legislator\LegislatorBundle\Form\CommentType;
use Legislator\LegislatorBundle\Form\ContentSectionType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
	        // check for group permission
	        if (!$document->canBeAccessed($this->getUser())) {
	        	throw new AccessDeniedException();
	        }
        }

        // TODO limit comments
        // TODO handle multiple versions
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

        $comment_form = $this->createForm(new CommentType(), $comment,
        		array('method' => 'post',
                	  'action' => $action));

        $document_form = $this->createForm(new DocumentStatusType(), $document,
        		array('method' => 'post'));

        // check privileges
        $is_document_owner = $document->isOwner($this->getUser());

        $can_take_comment_actions = $document->isStatusCommenting();
        $can_reply = $is_document_owner
        		&& $document->isStatusProcessingComments();
        $show_comment_actions = $document->isStatusCommenting()
        		|| ($is_document_owner && $document->isStatusProcessingComments());
        $show_add_comment = $document->getCanBeCommented();

        return $this->render('LegislatorBundle:Document:view.html.twig',
                array('document' => $document,
                      'comments' => $comments,
                      'form' => $comment_form->createView(),
                      'document_form' => $document_form->createView(),
                      'is_document_owner' => $is_document_owner,
                      'show_comment_actions' => $show_comment_actions,
                      'show_add_comment' => $show_add_comment,
                      'can_take_comment_actions' => $can_take_comment_actions,
                      'can_reply' => $can_reply));
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

        // check ownership
        if (!$document->isOwner($this->getUser())) {
            throw new AccessDeniedException();
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

        $until_date = new \DateTime();
        $until_date->modify('+14 days');
        $document->setCommentUntil($until_date);

        $form = $this->createForm(new DocumentNewType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $document->setStatus(Document::STATUS_NEW); // default value
            $document->setModifiedOn(new \DateTime());
            $document->setCreatedOn(new \DateTime());
            $user = $this->getUser();
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

    public function newVersionAction($id, Request $request)
    {
        $prev_document = $this->getDoctrine()
                    ->getRepository('LegislatorBundle:Document')->find($id);
        if (!$prev_document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        // check ownership
        if (!$prev_document->isOwner($this->getUser())) {
        	throw new AccessDeniedException();
        }

        // create form
        $document = new Document();
        $document->setName($prev_document->getName());
        $document->setDescription($prev_document->getDescription());
        $document->setVersion($prev_document->getVersion()+1);
        $document->setPreviousVersion($prev_document);

        $form = $this->createForm(new DocumentNewVersionType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $document->setStatus(Document::STATUS_NEW); // default value
            $document->setModifiedOn(new \DateTime());
            $document->setCreatedOn(new \DateTime());
            $user = $this->getUser();
            $document->setCreatedBy($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('legislator_homepage'));
        }

        // display form
        return $this->render('LegislatorBundle:Document:new.html.twig',
                array('form' => $form->createView(),
                      'prev_document' => $prev_document));
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

        if (!$document) {
            throw $this->createNotFoundException('No document found for id!');
        }

        // check ownership
        if (!$document->isOwner($this->getUser())) {
        	throw new AccessDeniedException();
        }

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
     * Process actions' form.
     *
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function processFormAction($id, Request $request)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);

        if ($document == null) {
            throw $this->createNotFoundException('No document found for id!');
        }

        // check ownership
        $user = $this->getUser();
        $is_document_owner = $user->getID() == $document->getCreatedBy()->getID();
        if (!$is_document_owner) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(new DocumentStatusType(), $document);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $document->setModifiedOn(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();
        }

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
    // TODO check if all comments have a reply
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

    /**
     * Download file attached to the document.
     * 
     * @param int $id
     * @param string $file
     */
    public function downloadAction($id, $file='main')
    {
    	function sanitize($filename)
    	{
    		$filename = strtolower($filename);
    		$filename = str_replace(' ', '_', $filename);
    		return $filename;
    	}
    	
    	$document = $this->getDoctrine()
    			->getRepository('LegislatorBundle:Document')->find($id);
    	if (!$document) {
    		throw $this->createNotFoundException('No document found for id!');
    	}

    	$options['absolute_path'] = FALSE;
    	$options['serve_filename'] = sanitize($document->getName());

    	$finfo = finfo_open(FILEINFO_MIME);
    	if (!$finfo) {
    		return $this->createNotFoundException('File not found');
    	}

    	if ($file == 'main') {
    		$path = $document->getWebPath();
    		$mimetype = finfo_file($finfo, $path);
    	} else {
    		$path = $document->getWebPathSubstantiation();
    		$mimetype = finfo_file($finfo, $path);
    		$options['serve_filename'] .= '_substantiation';
    	}

    	return $this->get('igorw_file_serve.response_factory')->create($path, $mimetype, $options);
    }
}
