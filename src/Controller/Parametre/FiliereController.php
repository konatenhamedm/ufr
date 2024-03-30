<?php

namespace App\Controller\Parametre;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
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

#[Route('/admin/parametre/filiere')]
class FiliereController extends AbstractController
{
    #[Route('/', name: 'app_parametre_filiere_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Filiere::class,
            ])
            ->setName('dt_app_parametre_filiere');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Filiere $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_filiere_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'target' => '#modal-xl',
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_filiere_delete', ['id' => $value]),
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


        return $this->render('parametre/filiere/index.html.twig', [
            'datatable' => $table
        ]);
    }
    #[Route('/config', name: 'app_parametre_filiere_config_index', methods: ['GET', 'POST'])]
    public function indexConfig(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Filiere::class,
            ])
            ->setName('dt_app_parametre_filiere');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Filiere $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_filiere_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'target' => '#modal-xl',
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_filiere_delete', ['id' => $value]),
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


        return $this->render('parametre/filiere/config_index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_filiere_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_filiere_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_filiere_index');




            if ($form->isValid()) {

                $entityManager->persist($filiere);
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
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('parametre/filiere/new.html.twig', [
            'filiere' => $filiere,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_filiere_show', methods: ['GET'])]
    public function show(Filiere $filiere): Response
    {
        return $this->render('parametre/filiere/show.html.twig', [
            'filiere' => $filiere,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_filiere_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiere $filiere, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(FiliereType::class, $filiere, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_filiere_edit', [
                'id' =>  $filiere->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_filiere_index');




            if ($form->isValid()) {

                $entityManager->persist($filiere);
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
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('parametre/filiere/edit.html.twig', [
            'filiere' => $filiere,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_filiere_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Filiere $filiere, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_filiere_delete',
                    [
                        'id' => $filiere->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($filiere);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_filiere_index');

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

        return $this->render('parametre/filiere/delete.html.twig', [
            'filiere' => $filiere,
            'form' => $form,
        ]);
    }
}
