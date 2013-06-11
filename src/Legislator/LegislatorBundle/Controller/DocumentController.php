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
    
    public function viewAction($id)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);
        if (!$document)
            throw $this->createNotFoundException('No document found for id!');

        // TODO limit comments
        $comments = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Comment')
                ->findBy(array('document' => $document));
        
        // add form to add comment
        $comment = new Comment();
        $comment->setDocument($document);
        $form = $this->createForm(new CommentType(), $comment, array(
                'method' => 'post',
                'action' => $this->generateUrl('legislator_comment_new',
                        array('id' => $id))));
        
        return $this->render('LegislatorBundle:Document:view.html.twig',
                array('document' => $document,
                      'comments' => $comments,
                      'form' => $form->createView()));
    }
    
    public function deleteAction($id)
    {
        $document = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Document')->find($id);
        if (!$document)
            throw $this->createNotFoundException('No document found for id!');
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();
        
        return $this->redirect($this->generateUrl('legislator_homepage'));
    }
    
    public function newAction(Request $request)
    {
        // create form
        $document = new Document();
        $document->setVersion(1); // default value

        $form = $this->createForm(new DocumentType(), $document);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            
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
            
        }

        // display form
        return $this->render('LegislatorBundle:Document:new.html.twig',
                        array('form' => $form->createView()));
    }

    public function editAction(Request $request)
    {
        $id = $request->get('id');
        $document = $this->getDoctrine()
                    ->getRepository('LegislatorBundle:Document')->find($id);

        if (!$document)
            throw $this->createNotFoundException('No document found for id!');

        $form = $this->createForm(new DocumentType(), $document);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
                
            if ($form->isValid()) {
                $document->setModifiedOn(new \DateTime());
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();
                
                return $this->redirect($this->generateUrl('legislator_homepage'));
            }
        }
        
        // display form
        return $this->render('LegislatorBundle:Document:edit.html.twig',
                array('form' => $form->createView(), 'id' => $id));

    }
}
