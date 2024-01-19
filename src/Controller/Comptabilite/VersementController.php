<?php

namespace App\Controller\Comptabilite;

use App\Controller\FileTrait;
use App\Entity\Inscription;
use App\Entity\Versement;
use App\Form\VersementType;
use App\Repository\VersementRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/admin/comptabilite/versement')]
class VersementController extends AbstractController
{
    use FileTrait;
    #[Route('/{id}/gestion', name: 'app_comptabilite_versement_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Inscription $inscription, DataTableFactory $dataTableFactory): Response
    {
        return $this->render('comptabilite/versement/index.html.twig', [
            'inscription' => $inscription
        ]);
    }


    #[Route('/{id}/historique', name: 'app_comptabilite_versement_historique', methods: ['GET', 'POST'])]
    #[Route('/historique', name: 'app_comptabilite_versement_historique_all', methods: ['GET', 'POST'])]
    public function historique(Request $request, UserInterface $user, DataTableFactory $dataTableFactory, ?Inscription $inscription = null): Response
    {
        $table = $dataTableFactory->create()
            ->add('dateVersement', DateTimeColumn::class, ['label' => 'Date', 'format' => 'd-m-Y'])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Montant'])
            ->add('frais', TextColumn::class, ['label' => 'Frais', 'field' => 'typeFrais.libelle'])
            ->add('nature', TextColumn::class, ['label' => 'Nature Rgt', 'field' => 'nature.libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Versement::class,
                'query' => function (QueryBuilder $qb) use ($inscription, $user) {
                    $qb->select(['v', 'nature', 'frais', 'typeFrais'])
                        ->from(Versement::class, 'v')
                        ->join('v.nature', 'nature')
                        ->join('v.fraisInscription', 'frais')
                        ->join('frais.typeFrais', 'typeFrais');

                    if ($inscription) {
                        $qb->andWhere('frais.inscription = :inscription')
                            ->setParameter('inscription', $inscription);
                    } else {
                        $qb
                            ->join('frais.inscription', 'inscription')
                            ->andWhere('inscription.etudiant= :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    };
                }
            ])
            ->setName('dt_app_comptabilite_versement_historique_' . $inscription?->getId());

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Versement $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'print' => [
                                'url' => $this->generateUrl('app_dashboard_iframe', [
                                    'r' => 'app_comptabilite_versement_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#modal-lg',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-success btn-stack']
                            ],
                            /*'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_versement_delete', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-trash',
                                'attrs' => ['class' => 'btn-danger'],
                                'render' => $renders['delete']
                            ]*/
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


        return $this->render('comptabilite/versement/historique.html.twig', [
            'datatable' => $table,
            'etudiant' => $user->getPersonne(),
            'inscription' => $inscription
        ]);
    }



    #[Route('/etudiant', name: 'app_comptabilite_versement_etudiant', methods: ['GET', 'POST'])]
    public function etudiant(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->createAdapter(ORMAdapter::class, [
                'entity' => Versement::class,
            ])
            ->setName('dt_app_comptabilite_versement_etudiant');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Versement $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_versement_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_versement_delete', ['id' => $value]),
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


        return $this->render('comptabilite/versement/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/{id}/new', name: 'app_comptabilite_versement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, VersementRepository $versementRepository, Inscription $inscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $versement = new Versement();
        $form = $this->createForm(VersementType::class, $versement, [
            'method' => 'POST',
            'inscription' => $inscription,
            'action' => $this->generateUrl('app_comptabilite_versement_new', ['id' => $inscription->getId()])
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $showAlert = false;
        $actions = [];
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_versement_historique', ['id' => $inscription->getId()]);




            if ($form->isValid()) {
                $versement->setReference($versementRepository->nextNumero($versement->getDateVersement()->format('Y')));
                $entityManager->persist($versement);
                $entityManager->flush();
                $redirectPrint = $this->getIframeUrl('app_comptabilite_versement_print', ['id' => $versement->getId()]);

                $actions = ['action' => 'open_modal', 'target' => '#modal-lg', 'url' => $redirectPrint];

                $data = true;
                $message       = 'Opération effectuée avec succès';
                $showAlert = true;
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'actions', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('comptabilite/versement/new.html.twig', [
            'versement' => $versement,
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_versement_show', methods: ['GET'])]
    public function show(Versement $versement): Response
    {
        return $this->render('comptabilite/versement/show.html.twig', [
            'versement' => $versement,
        ]);
    }


    #[Route('/{id}/print-recu', name: 'app_comptabilite_versement_print', methods: ['GET'])]
    public function printRecu(Versement $versement): Response
    {
        return $this->renderPdf('comptabilite/versement/print_recu_versement.html.twig', [
            'versement' => $versement,
        ],  [

            'format' => 'A4',
            'orientation' => 'P',
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ]);
    }


    #[Route('/{id}/edit', name: 'app_comptabilite_versement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Versement $versement, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(VersementType::class, $versement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_versement_edit', [
                'id' =>  $versement->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_versement_index');




            if ($form->isValid()) {

                $entityManager->persist($versement);
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

        return $this->render('comptabilite/versement/edit.html.twig', [
            'versement' => $versement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_versement_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Versement $versement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_versement_delete',
                    [
                        'id' => $versement->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($versement);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilite_versement_index');

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

        return $this->render('comptabilite/versement/delete.html.twig', [
            'versement' => $versement,
            'form' => $form,
        ]);
    }
}
