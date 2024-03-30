<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class InscriptionMenuBuilder
{
    private $factory;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private const MODULE_NAME = 'Caissiere';

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
            $menu->addChild(self::MODULE_NAME, ['label' => 'Inscriptions']);
        }



        if (isset($menu[self::MODULE_NAME])) {

            //$menu->addChild('parametre.index', ['route' => 'app_parametre_preinscription_index', 'label' => 'Préinscriptions'])->setExtra('icon', 'bi bi-gear')->setExtra('role', 'ROLE_CAISSIERE');
            $menu->addChild('echeancier.index', ['route' => 'app_config_echeanciers', 'label' => 'Écheanciers'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_CAISSIERE');
            $menu->addChild('scolarite.index', ['route' => 'app_config_scolarite', 'label' => 'Scolarités'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_CAISSIERE');
            //$menu->addChild('personne.index', ['route' => 'app_inscription_inscription_frais_index', 'label' => 'Frais de scolarité'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_CAISSIERE');
            /*   $menu->addChild('paiement', ['route' => 'app_config_preinscription_point_paiement_index', 'label' => 'Point des paiements'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_SECRETAIRE');
            $menu->addChild('cheque', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements à confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_CAISSIERE');
 */
            /*  if (!$this->user->hasRoleIn("ROLE_ADMIN")) {

                $menu->addChild('cheque_secretaire', ['route' => 'app_config_preinscription_point_paiement_cheque_index', 'label' => 'Paiements avec confirmer'])->setExtra('icon', 'bi bi-cash')->setExtra('role', 'ROLE_SECRETAIRE');
            } */
            //$menu->addChild('workflow.index', ['route' => 'app_home_timeline_index', 'label' => 'MES DOSSIERS'])->setExtra('icon', 'bi bi-person')->setExtra('role', 'ROLE_INSCRIPTION_CAISSIERE');
            // $menu->addChild('groupe.index', ['route' => 'app_utilisateur_groupe_index', 'label' => 'Groupes'])->setExtra('icon', 'bi bi-people-fill');
            //$menu->addChild('utilisateur.index', ['route' => 'app_utilisateur_utilisateur_index', 'label' => 'Utilisateurs'])->setExtra('icon', 'bi bi-person-fill');
        }

        return $menu;
    }
}
