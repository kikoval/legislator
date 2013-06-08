<?php

namespace Legislator\LegislatorBundle\Controller;

use Legislator\LegislatorBundle\Entity\Comment;
use Legislator\LegislatorBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController extends Controller {

	/**
	 * Processes submitted form for adding new comment.
	 *
	 * @param int $id Document ID
	 * @param Request $request
	 * @throws NotFoundHttpException
	 */
	public function newAction($id, Request $request)
	{
		$comment = new Comment();

		$doc_id = $id;
		if ($doc_id == null) {
			throw new NotFoundHttpException();
		}
		$document = $this->getDoctrine()
		->getRepository('LegislatorBundle:Document')->find($doc_id);
		if ($document == null) {
			throw new NotFoundHttpException();
		}
		$comment->setDocument($document);

		$form = $this->createForm(new CommentType(), $comment);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$comment->setCreatedOn(new \Datetime());
			$user = $this->get('security.context')->getToken()->getUser();
			$comment->setCreatedBy($user);

			$em = $this->getDoctrine()->getManager();
			$em->persist($comment);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('legislator_view',
				array('id' => $comment->getDocument()->getId())));
	}

	public function deleteAction($id, Request $request)
	{
		$comment = $this->getDoctrine()
			->getRepository('LegislatorBundle:Comment')->find($id);
		if ($comment == null)
			throw $this->createNotFoundException('No comment found for id!');

		$doc_id = $comment->getDocument()->getId();

		$em = $this->getDoctrine()->getManager();
		$em->remove($comment);
		$em->flush();

		return $this->redirect($this->generateUrl('legislator_view',
				array('id' => $doc_id)));
	}
}
