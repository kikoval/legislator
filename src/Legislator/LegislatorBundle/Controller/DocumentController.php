<?php

namespace Legislator\LegislatorBundle\Controller;
use Symfony\Component\HttpFoundation\Response;

use Legislator\LegislatorBundle\Entity\Document;
use Legislator\LegislatorBundle\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DocumentController extends Controller {
	
	public function viewAction($id)
	{
		$document = $this->getDoctrine()
				->getRepository('LegislatorBundle:Document')->find($id);
		if (!$document)
			throw $this->createNotFoundException('No document found for id!');

		return $this
				->render('LegislatorBundle:Document:view.html.twig',
						array('document' => $document));
	}

	public function deleteAction($id)
	{
		$document = $this->getDoctrine()
				->getRepository('LegislatorBundle:Document')->find($id);
		if (!$document)
			throw $this->createNotFoundException('No document found for id!');
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($document);
		
		return $this->redirect($this->generateUrl('legislator_homepage'));
	}
	
	public function commentAction($id) {
		// TODO
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
				array('form' => $form->createView()));

	}
}
