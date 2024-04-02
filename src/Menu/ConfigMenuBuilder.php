<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class ConfigMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Config';

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
        if ($this->user->hasRoleIn('ROLE_SECRETAIRE')) {
            $menu->addChild(self::MODULE_NAME, ['label' => 'Configuration']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('parametre.index', ['route' => 'app_parametre_dashboard_index', 'label' => 'Paramètres'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('personne.index', ['route' => 'app_utilisateur_personne_index', 'label' => 'Personnels'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('groupe.index', ['route' => 'app_utilisateur_groupe_index', 'label' => 'Groupes'])->setExtra('icon', 'bi bi-people-fill')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('utilisateur.index', ['route' => 'app_config_utilisateur_index', 'label' => 'Utilisateurs'])->setExtra('icon', 'bi bi-person-fill')->setExtra('role', 'ROLE_SECRETAIRE');
        }

        return $menu;
    }
}
