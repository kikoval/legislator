<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($archive = FALSE)
    {
    	$documents = $this->getDoctrine()
    		->getRepository('LegislatorBundle:Document')->findBy(
    			array('is_archived' => $archive)
    	);

    	$documents_show = $documents;
    	if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
    		// removing documents that cannot be accessed
    		$documents_show = array();
	    	foreach ($documents as $d) {
	    		if ($d->canBeAccessed($this->getUser())) {
	    			array_push($documents_show, $d);
	    		}
	    	}
    	}

        return $this->render('LegislatorBundle:Default:index.html.twig',
        		array('documents' => $documents_show,
        		      'can_add_document' => $this->get('security.context')->isGranted('ROLE_USER'),
        			  'is_archive' => $archive
        ));
    }

    public function showArchiveAction()
    {
    	return $this->indexAction(TRUE);
    }
}
