<?php

namespace Legislator\MyFOSUserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{

    public function registerAction(Request $request)
    {
        if ($this->container->getParameter('cosign_login_enabled')) {
            throw new AccessDeniedException("Registration is disabled.");
        }

        return parent::registerAction($request);
    }
}
