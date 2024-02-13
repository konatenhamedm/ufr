<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class EspaceEtudiantMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'espace';

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
        if ($this->user->hasRole('ROLE_ETUDIANT')) {
            $menu->addChild(self::MODULE_NAME, ['label' => '']);
        }

        if (isset($menu[self::MODULE_NAME])) {
            $menu->addChild('information', ['route' => 'site_information', 'label' => 'Mes informations'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_ETUDIANT');
            $menu->addChild('workflow.index', ['route' => 'app_home_timeline_etudiant_index', 'label' => 'Suivi  dossiers'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_ETUDIANT');
            //$menu->addChild('document', ['route' => 'site_document', 'label' => 'Mes documents'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_ETUDIANT');
            $menu->addChild('preinscription.index', ['route' => 'app_config_preinscription_formation_index', 'label' => 'Mes formations'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_ETUDIANT');
            $menu->addChild('note.index', ['route' => 'app_utilisateur_personne_index', 'label' => 'Mes notes'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_ETUDIANT');
            $menu->addChild('scolarite.index', ['route' => 'app_utilisateur_personne_index', 'label' => 'Ma scolarité'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_ETUDIANT');
            // $menu->addChild('groupe.index', ['route' => 'app_utilisateur_groupe_index', 'label' => 'Groupes'])->setExtra('icon', 'bi bi-people-fill');
            //$menu->addChild('utilisateur.index', ['route' => 'app_utilisateur_utilisateur_index', 'label' => 'Utilisateurs'])->setExtra('icon', 'bi bi-person-fill');
        }

        return $menu;
    }
}
