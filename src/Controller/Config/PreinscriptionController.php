<?php

namespace App\Controller\Config;

use App\Attribute\Module;
use App\Attribute\RoleMethod;
use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('config/preinscription')]
#[Module(name: 'config', excludes: ['liste'])]
class PreinscriptionController extends AbstractController
{

    #[Route(path: '/', name: 'app_config_preinscription_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'En attente de traitement ',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'attente_validation'])
            ],
            [
                'label' => 'En attente de paiement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Préinscriptions payées',
                'icon' => 'bi bi-list',
                'module' => 'gestiono',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_solde_index')
            ],
            [
                'label' => 'En attente de complement',
                'icon' => 'bi bi-list',
                'module' => 'gestionk',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'attente_informations'])
            ],
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/preinscription/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }
    #[Route(path: '/suivi/{id}', name: 'app_config_preinscription_suivi_formation_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexSuivi(Request $request, Breadcrumb $breadcrumb, $id): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'En attente de traitement ',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_suivi_formation_index', ['etat' => 'attente_validation', 'id' => $id])
            ],
            [
                'label' => 'En attente de paiement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index', ['id' => $id])
            ],
            [
                'label' => 'En attente de complement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_suivi_formation_index', ['etat' => 'attente_informations', 'id' => $id])
            ],
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/preinscription/index_suivi_formation.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }


    #[Route(path: '/{module}', name: 'app_config_preinscription_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $configs = [];


        return $this->render('config/preinscription/liste.html.twig', ['links' => $configs[$module] ?? []]);
    }
}
