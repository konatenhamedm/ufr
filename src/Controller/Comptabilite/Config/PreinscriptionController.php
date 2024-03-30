<?php

namespace App\Controller\Comptabilite\Config;

use App\Attribute\Module;
use App\Attribute\RoleMethod;
use App\Service\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/parametre/preinscription')]
#[Module(name: 'config', excludes: ['liste'])]
class PreinscriptionController extends AbstractController
{

    #[Route(path: '/', name: 'app_parametre_preinscription_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Préinscriptions', as: 'index')]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'En attente de paiement',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_index')
            ],
            [
                'label' => 'Point des paiements',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ],
            /* [
                'label' => 'Payés',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_preinscription_ls', ['etat' => 'valide'])
            ], */
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

        return $this->render('parametre/dashboard/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,
            'title' => 'Paiements de préinscriptions'
        ]);
    }

    #[Route(path: '/{module}', name: 'app_parametre_preinscription_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, string $module): Response
    {
        /**
         * @todo: A déplacer dans un service
         */
        $parametres = [
            'valider' => [
                [
                    'label' => 'PAIEMENTS PREINSCRIPTIONS',
                    'id' => 'param_non_paye',
                    'href' => $this->generateUrl('app_comptabilite_inscription_index')
                ],
                [
                    'label' => 'PAIEMENTS SCOLARITE',
                    'id' => 'param_scolarite',
                    'href' => $this->generateUrl('app_comptabilite_inscription_index')
                ],
                /*, [
                    'label' => 'Attente confirmation',
                    'id' => 'param_paye',
                    'href' => $this->generateUrl('app_comptabilite_preinscription_ls', ['etat' => 'paiement_confirmation'])

                ]
                [
                    'label' => 'Réjetés',
                    'id' => 'param_rejeter',
                    'href' => $this->generateUrl('app_comptabilite_preinscription_ls', ['etat' => 'rejete'])

                ]*/
            ],

        ];


        return $this->render('parametre/dashboard/liste.html.twig', ['links' => $parametres[$module] ?? []]);
    }
}
