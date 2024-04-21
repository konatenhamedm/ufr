<?php

namespace App\Controller\Etudiant\Echenacier;

use App\Entity\BlocEcheancier;
use App\Form\BlocEcheancierType;
use App\Repository\BlocEcheancierRepository;
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

#[Route('/admin/etudiant/echenacier/bloc/echeancier')]
class BlocEcheancierController extends AbstractController
{
    #[Route('/', name: 'app_etudiant_echenacier_bloc_echeancier_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->createAdapter(ORMAdapter::class, [
            'entity' => BlocEcheancier::class,
        ])
        ->setName('dt_app_etudiant_echenacier_bloc_echeancier');

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
                , 'render' => function ($value, BlocEcheancier $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_delete', ['id' => $value]),
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


        return $this->render('etudiant/echenacier/bloc_echeancier/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_etudiant_echenacier_bloc_echeancier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $blocEcheancier = new BlocEcheancier();
        $form = $this->createForm(BlocEcheancierType::class, $blocEcheancier, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_index');




            if ($form->isValid()) {

                $entityManager->persist($blocEcheancier);
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

        return $this->render('etudiant/echenacier/bloc_echeancier/new.html.twig', [
            'bloc_echeancier' => $blocEcheancier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_etudiant_echenacier_bloc_echeancier_show', methods: ['GET'])]
    public function show(BlocEcheancier $blocEcheancier): Response
    {
        return $this->render('etudiant/echenacier/bloc_echeancier/show.html.twig', [
            'bloc_echeancier' => $blocEcheancier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_echenacier_bloc_echeancier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BlocEcheancier $blocEcheancier, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(BlocEcheancierType::class, $blocEcheancier, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_edit', [
                    'id' =>  $blocEcheancier->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

       if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_index');




            if ($form->isValid()) {

                $entityManager->persist($blocEcheancier);
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

        return $this->render('etudiant/echenacier/bloc_echeancier/edit.html.twig', [
            'bloc_echeancier' => $blocEcheancier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_etudiant_echenacier_bloc_echeancier_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, BlocEcheancier $blocEcheancier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_etudiant_echenacier_bloc_echeancier_delete'
                ,   [
                        'id' => $blocEcheancier->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($blocEcheancier);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_etudiant_echenacier_bloc_echeancier_index');

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

        return $this->render('etudiant/echenacier/bloc_echeancier/delete.html.twig', [
            'bloc_echeancier' => $blocEcheancier,
            'form' => $form,
        ]);
    }
}
