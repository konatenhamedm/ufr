<?php

namespace App\Controller\Etudiant\Echenacier;

use App\Entity\EcheancierProvisoire;
use App\Form\EcheancierProvisoireType;
use App\Repository\EcheancierProvisoireRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/etudiant/echenacier/echeancier/provisoire')]
class EcheancierProvisoireController extends AbstractController
{
    #[Route('/', name: 'app_etudiant_echenacier_echeancier_provisoire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => EcheancierProvisoire::class,
        ])
        ->setName('dt_app_etudiant_echenacier_echeancier_provisoire');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];


        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, EcheancierProvisoire $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_delete', ['id' => $value]),
                            'ajax' => true,
                            'stacked' => false,
                            'icon' => '%icon% bi bi-trash',
                            'attrs' => ['class' => 'btn-danger'],
                            'render' => $renders['delete']
                        ]
                    ]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('etudiant/echenacier/echeancier_provisoire/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_etudiant_echenacier_echeancier_provisoire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $echeancierProvisoire = new EcheancierProvisoire();
        $form = $this->createForm(EcheancierProvisoireType::class, $echeancierProvisoire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_index');




            if ($form->isValid()) {

                $entityManager->persist($echeancierProvisoire);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->render('etudiant/echenacier/echeancier_provisoire/new.html.twig', [
            'echeancier_provisoire' => $echeancierProvisoire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_etudiant_echenacier_echeancier_provisoire_show', methods: ['GET'])]
    public function show(EcheancierProvisoire $echeancierProvisoire): Response
    {
        return $this->render('etudiant/echenacier/echeancier_provisoire/show.html.twig', [
            'echeancier_provisoire' => $echeancierProvisoire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_echenacier_echeancier_provisoire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EcheancierProvisoire $echeancierProvisoire, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(EcheancierProvisoireType::class, $echeancierProvisoire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_edit', [
                    'id' =>  $echeancierProvisoire->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

       if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_index');




            if ($form->isValid()) {

                $entityManager->persist($echeancierProvisoire);
                $entityManager->flush();

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);


            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }

            }

            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }

        }

        return $this->render('etudiant/echenacier/echeancier_provisoire/edit.html.twig', [
            'echeancier_provisoire' => $echeancierProvisoire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_etudiant_echenacier_echeancier_provisoire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, EcheancierProvisoire $echeancierProvisoire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_etudiant_echenacier_echeancier_provisoire_delete'
                ,   [
                        'id' => $echeancierProvisoire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($echeancierProvisoire);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_etudiant_echenacier_echeancier_provisoire_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
                'data' => $data
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }
        }

        return $this->render('etudiant/echenacier/echeancier_provisoire/delete.html.twig', [
            'echeancier_provisoire' => $echeancierProvisoire,
            'form' => $form,
        ]);
    }
}
