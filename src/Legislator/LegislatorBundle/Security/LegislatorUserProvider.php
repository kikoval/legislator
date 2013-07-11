<?php

namespace Legislator\LegislatorBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class LegislatorUserProvider implements UserProviderInterface
{

public function __construct(UserManagerInterface $userManager, $cosign_login_enabled=FALSE)
{
  $this->userManager = $userManager;
  $this->cosign_login_enabled = $cosign_login_enabled;
}

public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);

	if ($this->cosign_login_enabled) {
	    if ($user === null) {
                // TODO check na fakultu
                $user = $this->userManager->createUser();
                $user->setUsername($username);
                $user->setEmail("$username@uniba.sk");
                $user->setPassword("$username@uniba.sk");
                $user->setEnabled(1);
                // TODO set full name from LDAP
                $user->setFirstName("");
                $user->setSurname($username);

                $this->userManager->updateUser($user);
            }
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
