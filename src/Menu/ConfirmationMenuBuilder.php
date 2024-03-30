<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;


class ConfirmationMenuBuilder
{

    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'confirmation';

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
        if ($this->user->hasRoleInExept("ROLE_CAISSIERE") ||  $this->user->hasRoleInExept('ROLE_SECRETAIRE')) {
            $menu->addChild('paiement.index', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'PAIEMENT AVEC CONFIRMATION'])
                ->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_CAISSIERE');
        }
        /*    if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('paiement.index', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'PAIEMENT AVEC CONFIRMATION'])
                ->setExtra('icon', 'bi bi-cash')
                ->setExtra('role', 'ROLE_ALL');
        } */


        return $menu;
    }
}
