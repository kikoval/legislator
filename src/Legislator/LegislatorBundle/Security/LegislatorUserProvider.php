<?php

namespace Legislator\LegislatorBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;


class LegislatorUserProvider implements UserProviderInterface {

	public function __construct(UserManagerInterface $userManager, ContainerInterface $container)
	{
	  $this->userManager = $userManager;
	  $this->container = $container;

	  $this->cosign_login_enabled = $this->container->getParameter('cosign_login_enabled');
	}

	public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);

		if ($this->cosign_login_enabled) {
			$ldapSearch = $this->container->get('legislator.user_search');
			$user_info = $ldapSearch->byLogin($username);
			if (!array_key_exists($username, $user_info)) {
				throw new UsernameNotFoundException(sprintf('Username "%s" not found in LDAP.', $username));
			}
			$user_info = $user_info[$username];

			$org_unit = $this->container->getParameter('org_unit');

			// checking org unit if set
			if ($org_unit !== null
	        		&& array_search($org_unit, $user_info['orgUnits']) === FALSE) {
	        	throw new AccessDeniedException(sprintf('Username "%s" does not belong to unit "%s".', $username, $org_unit));
	        }

	        // creating a new user when logging in for the first time
		    if ($user === null) {
                $user = $this->userManager->createUser();
                $user->setUsername($username);
                $user->setEmail("$username@uniba.sk");
                $user->setPassword("$username@uniba.sk");
                $user->setEnabled(1);
			}

			// set full name from LDAP
			$user->setFirstName($user_info['givenName']);
			$user->setSurname($user_info['familyName']);

			$this->userManager->updateUser($user);

		} else {
            if (!$user) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }
		}

        return $user;
    }

	/**
     * {@inheritDoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof User && !$user instanceof PropelUser) {
            throw new UnsupportedUserException(sprintf('Expected an instance of FOS\UserBundle\Model\User, but got "%s".', get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(array('id' => $user->getId()))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    /**
     * Finds a user by username.
     *
     * This method is meant to be an extension point for child classes.
     *
     * @param string $username
     *
     * @return UserInterface|null
     */
    protected function findUser($username)
    {
        return $this->userManager->findUserByUsername($username);
    }

}

?>
