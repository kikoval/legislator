<?php

namespace Legislator\LegislatorBundle\Controller;

use FOS\UserBundle\Controller\GroupController as ParentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupController extends ParentController
{

	/**
	 * Show one group
	 */
	public function showAction($groupName)
	{
		$group = $this->findGroupBy('name', $groupName);
		$users = $group->getUsers();

		return $this->container->get('templating')->renderResponse('LegislatorBundle:Group:show.html.twig', array('group' => $group, 'users' => $users));
	}

	public function addUserAction($groupName, Request $request)
	{
		$group = $this->findGroupBy('name', $groupName);

		$username = $request->get('username');
		$user = $this->container->get('doctrine.orm.entity_manager')
				->getRepository('LegislatorBundle:User')
				->findOneBy(array('username' => $username));

		if (is_null($user)) {
			// user does not exist, try to load it from LDAP (if enabled)
			$user_provider = $this->container->get('legislator_user.provider');
			$user = $user_provider->loadUserByUsername($username);

// 			throw new NotFoundHttpException('No user found for username "'.$username.'"!');
		}

		// user exists, just add it to the group
		$user->addGroup($group);

		$em = $this->container->get('doctrine.orm.entity_manager');
		$em->persist($user);
		$em->flush();

		$url = $this->container->get('router')->generate('fos_user_group_show', array('groupName' => $group->getName()));
		return new RedirectResponse($url);
	}

}
