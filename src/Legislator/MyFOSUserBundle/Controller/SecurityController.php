<?php

namespace Legislator\MyFOSUserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController
{
    public function loginAction(Request $request)
    {
        if ($this->container->getParameter('cosign_login_enabled')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            if ($user !== null) {
                $router = $this->container->get('router');

                return new RedirectResponse($router->generate('legislator_homepage'));
            }
        }

        return parent::loginAction($request);
    }
}
