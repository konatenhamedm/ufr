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
                'label' => 'En attente de finalisation ',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'attente_paiement'])
            ],
            [
                'label' => 'Finalisées',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'valide'])
            ],
            [
                'label' => 'En attente confirmation',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'paiement_confirmation'])
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


    #[Route(path: '/{module}', name: 'app_config_preinscription_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $configs = [

        ]
        ;


        return $this->render('config/preinscription/liste.html.twig', ['links' => $configs[$module] ?? []]);
    }
}