<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

use Psr\Container\ContainerInterface;


class ServiceRouter
{


    private mixed $route;

    private $security;


    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, RouterInterface $router, Security $security)
    {

        if ($requestStack->getCurrentRequest()) {
            $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');

        }

    }

    public  function  getUser(){
        return $this->security->getUser();
    }

    //    public function verifyanddispatch() {
    //
    //
    //
    //    }
}
