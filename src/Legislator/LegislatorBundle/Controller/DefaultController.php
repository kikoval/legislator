<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$documents = $this->getDoctrine()
    		->getRepository('LegislatorBundle:Document')->findAll();

        return $this->render('LegislatorBundle:Default:index.html.twig',
        		array('documents' => $documents,
        		      'can_add_document' => $this->get('security.context')->isGranted('ROLE_USER')));
    }
}
