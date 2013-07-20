<?php

namespace Legislator\LegislatorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Legislator\LegislatorBundle\Entity\User;


class LoadUserData implements FixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$userAdmin = new User();
		$userAdmin->setUsername('admin');
		$userAdmin->setPlainPassword('test');
		$userAdmin->addRole("ROLE_ADMIN");
		$userAdmin->setEmail('test@example.com');
		$userAdmin->setFirstName('ad');
		$userAdmin->setSurname('min');
		$userAdmin->setEnabled(TRUE);

		$manager->persist($userAdmin);
		$manager->flush();
	}
}