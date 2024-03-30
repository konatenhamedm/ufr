<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class ComptabiliteSecretaireMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'admin';

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
        if ($this->user->hasRoleInExept("ROLE_SECRETAIRE")) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Comptabilité']);
        }



        if (isset($menu[self::MODULE_NAME])) {

            $menu->addChild('parametre.index', ['route' => 'app_parametre_preinscription_index', 'label' => 'Points des paiements'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_CAISSIERE');
        }

        return $menu;
    }
}
