<?php

namespace Legislator\LegislatorBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Legislator\LegislatorBundle\Entity\User;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
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
		$userAdmin->setEmail('admin@example.com');
		$userAdmin->setFirstName('ad');
		$userAdmin->setSurname('min');
		$userAdmin->setEnabled(TRUE);

		$manager->persist($userAdmin);
		
		$userTest = new User();
		$userTest->setUsername('tester');
		$userTest->setPlainPassword('test');
		$userTest->addRole("ROLE_USER");
		$userTest->setEmail('test@example.com');
		$userTest->setFirstName('testing');
		$userTest->setSurname('tester');
		$userTest->setEnabled(TRUE);
		
		$manager->persist($userTest);
		
		$manager->flush();
		
		$this->setReference('test-user', $userTest);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 1;
	}
}