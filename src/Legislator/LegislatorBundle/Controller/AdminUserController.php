<?php

namespace Legislator\LegislatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminUserController extends Controller
{

    public function listAction()
    {
    	$users = $this->getDoctrine()
                ->getRepository('LegislatorBundle:User')->findAll();

    	$groups = $this->get('fos_user.group_manager')->findGroups();

    	return $this->render('LegislatorBundle:AdminUser:list.html.twig',
				array('users' => $users, 'groups' => $groups));
    }

    public function processGroupFromAction($id)
    {
    	$request = $this->getRequest();
    	$group_id = $request->get('group_id');

    	$user = $this->getDoctrine()
                ->getRepository('LegislatorBundle:User')->find($id);
    	if ($user === null) {
    		throw $this->createNotFoundException('No user found for id!');
    	}

    	$group = $this->getDoctrine()
                ->getRepository('LegislatorBundle:Group')->find($group_id);
    	if ($group === null) {
    		throw $this->createNotFoundException('No group found for id!');
    	}

    	if ($request->get('add_to_group')) {
    		$user->addGroup($group);
    	} elseif ($request->get('remove_from_group')) {
    		$user->removeGroup($group);
    	}

    	$em = $this->getDoctrine()->getEntityManager();
    	$em->persist($user);
    	$em->flush();

    	return $this->redirect($this->generateUrl('legislator_user_list'));
    }

    public function processAdminRoleFormAction($id)
    {
    	$request = $this->getRequest();

    	$user = $this->getDoctrine()
    			->getRepository('LegislatorBundle:User')->find($id);
    	if ($user === null) {
    		throw $this->createNotFoundException('No user found for id!');
    	}

    	if ($request->get('add_admin_role')) {
    		$user->addRole("ROLE_ADMIN");
    	} elseif ($request->get('remove_admin_role')) {
    		$user->removeRole("ROLE_ADMIN");
    	}
    	$em = $this->getDoctrine()->getEntityManager();
    	$em->persist($user);
    	$em->flush();

    	return $this->redirect($this->generateUrl('legislator_user_list'));
    }

}
