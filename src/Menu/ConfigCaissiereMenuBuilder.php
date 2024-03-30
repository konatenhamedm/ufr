<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class ConfigCaissiereMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Comptable';

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

            $menu->addChild(self::MODULE_NAME, ['label' => 'Configuration']);
        }
        // dd($this->user->hasRoleOnModule('comptable'));

        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('type.index', ['route' => 'app_parametre_type_frais_config_index', 'label' => 'Type de frais'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('nature.index', ['route' => 'app_parametre_nature_paiement_config_index', 'label' => 'Nature des paiements'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('filiere.index', ['route' => 'app_parametre_filiere_config_index', 'label' => 'Filières'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_COMPTABLE');
            $menu->addChild('niveau.index', ['route' => 'app_parametre_niveau_config_index', 'label' => 'Niveaux par filière'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_COMPTABLE');
        }

        return $menu;
    }
}
