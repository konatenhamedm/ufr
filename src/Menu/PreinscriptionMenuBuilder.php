<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;


class PreinscriptionMenuBuilder
{

    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'caissiere';

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->user = $security->getUser();
    }
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setExtra('module', self::MODULE_NAME);
        if ($this->user->hasRoleInExept('ROLE_COMPTABLE')) {
            $menu->addChild('dashboard.index', ['route' => 'app_parametre_preinscription_index', 'label' => 'PRÉINSCRIPTIONS'])
                ->setExtra('icon', 'bi bi-house')->setExtra('role', 'ROLE_CAISSIERE');
        }
        /*   if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('dashboard.index', ['route' => 'app_parametre_preinscription_index', 'label' => 'PRÉINSCRIPTIONS'])
                ->setExtra('icon', 'bi bi-house')->setExtra('role', 'ROLE_CAISSIERE');
        } */


        return $menu;
    }
}
