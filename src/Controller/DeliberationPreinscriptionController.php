<?php

namespace App\Controller;

use App\Entity\Deliberation;
use App\Entity\DeliberationPreinscription;
use App\Entity\LigneDeliberation;
use App\Entity\Preinscription;
use App\Form\DeliberationPreinscription1Type;
use App\Repository\DeliberationPreinscriptionRepository;
use App\Repository\LigneDeliberationRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
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

#[Route('/admin/deliberation/preinscription')]
class DeliberationPreinscriptionController extends AbstractController
{
    #[Route('/', name: 'app_deliberation_preinscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, UserInterface $user): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['field' => 'prescription.code', 'label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.code', 'label' => 'Filière'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
            ->add('dateExamen', DateTimeColumn::class, ['field' => 'd.dateExamen', 'label' => 'Date examen', 'format' => 'd-m-Y'])
            ->add('examen', TextColumn::class, ['field' => 'e.libelle', 'label' => 'Examen'])
            ->add('total', TextColumn::class, ['field' => 'd.total', 'label' => 'Total'])
            ->add('moyenne', TextColumn::class, ['field' => 'd.moyenne', 'label' => 'Moyenne'])
            ->add('mention', TextColumn::class, ['field' => 'm.libelle', 'label' => 'Mention'])
            ->add('etat', TextColumn::class, ['field' => 'd.etat', 'label' => 'Etat']);


        /*    if (!$isEtudiant) {
            $table->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Preinscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
        }
        if ($etat == 'valide') {
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']);
            $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'info.datePaiement', 'format' => 'd-m-Y']);
        } */
        $table->createAdapter(ORMAdapter::class, [
            'entity' => DeliberationPreinscription::class,
            'query' => function (QueryBuilder $qb) use ($user) {
                $qb->select(['p', 'niveau',  'filiere', 'etudiant', 'd', 'm', 'e', 'prescription'])
                    ->from(DeliberationPreinscription::class, 'p')
                    ->join('p.preinscription', 'prescription')
                    ->join('prescription.etudiant', 'etudiant')
                    ->leftJoin('p.deliberation', 'd')
                    ->leftJoin('d.mention', 'm')
                    ->leftJoin('d.examen', 'e')
                    ->join('e.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->orWhere('prescription.etatDeliberation = :etat')
                    ->setParameter('etat', 'deliberer');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('prescription.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
            }
        ])
            ->setName('dt_app_deliberation_preinscription');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'show' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, DeliberationPreinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_deliberation_preinscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_direction_deliberation_show', ['id' => $context->getDeliberation()->getId()]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_deliberation_preinscription_delete', ['id' => $value]),
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


        return $this->render('deliberation_preinscription/index.html.twig', [
            'datatable' => $table
        ]);
    }
    #[Route('/formation/{id}', name: 'app_deliberation_preinscription_formation_index', methods: ['GET', 'POST'])]
    public function indexFormation(Request $request, DataTableFactory $dataTableFactory, UserInterface $user, $id): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['field' => 'prescription.code', 'label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.code', 'label' => 'Filière'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
            ->add('dateExamen', DateTimeColumn::class, ['field' => 'd.dateExamen', 'label' => 'Date examen', 'format' => 'd-m-Y'])
            ->add('examen', TextColumn::class, ['field' => 'e.libelle', 'label' => 'Examen'])
            ->add('total', TextColumn::class, ['field' => 'd.total', 'label' => 'Total'])
            ->add('moyenne', TextColumn::class, ['field' => 'd.moyenne', 'label' => 'Moyenne'])
            ->add('mention', TextColumn::class, ['field' => 'm.libelle', 'label' => 'Mention'])
            ->add('etat', TextColumn::class, ['field' => 'd.etat', 'label' => 'Etat']);


        /*    if (!$isEtudiant) {
            $table->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Preinscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
        }
        if ($etat == 'valide') {
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']);
            $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'info.datePaiement', 'format' => 'd-m-Y']);
        } */
        $table->createAdapter(ORMAdapter::class, [
            'entity' => DeliberationPreinscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $id) {
                $qb->select(['p', 'niveau',  'filiere', 'etudiant', 'd', 'm', 'e', 'prescription'])
                    ->from(DeliberationPreinscription::class, 'p')
                    ->join('p.preinscription', 'prescription')
                    ->join('prescription.etudiant', 'etudiant')
                    ->leftJoin('p.deliberation', 'd')
                    ->leftJoin('d.mention', 'm')
                    ->leftJoin('d.examen', 'e')
                    ->join('e.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->orWhere('prescription.etatDeliberation = :etat')
                    ->setParameter('etat', 'deliberer');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('prescription.etudiant = :etudiant')
                        ->andWhere('prescription.id = :id')
                        ->setParameter('etudiant', $user->getPersonne())
                        ->setParameter('id', $id);
                }
            }
        ])
            ->setName('dt_app_deliberation_preinscription' . $id);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'show' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, DeliberationPreinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_deliberation_preinscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_direction_deliberation_show', ['id' => $context->getDeliberation()->getId()]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_deliberation_preinscription_delete', ['id' => $value]),
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


        return $this->render('deliberation_preinscription/index_suivi.html.twig', [
            'datatable' => $table,
            'id' => $id
        ]);
    }


    #[Route('/new', name: 'app_deliberation_preinscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $deliberationPreinscription = new DeliberationPreinscription();
        $form = $this->createForm(DeliberationPreinscription1Type::class, $deliberationPreinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_deliberation_preinscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_deliberation_preinscription_index');




            if ($form->isValid()) {

                $entityManager->persist($deliberationPreinscription);
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

        return $this->render('deliberation_preinscription/new.html.twig', [
            'deliberation_preinscription' => $deliberationPreinscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_deliberation_preinscription_show', methods: ['GET'])]
    public function show(Deliberation $ligneDeliberation, LigneDeliberationRepository $ligneDeliberationRepository): Response
    {
        return $this->render('deliberation_preinscription/show.html.twig', [
            'deliberation_preinscription' => $ligneDeliberationRepository->findBy(['deliberation' => $ligneDeliberation]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deliberation_preinscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DeliberationPreinscription $deliberationPreinscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(DeliberationPreinscription1Type::class, $deliberationPreinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_deliberation_preinscription_edit', [
                'id' =>  $deliberationPreinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_deliberation_preinscription_index');




            if ($form->isValid()) {

                $entityManager->persist($deliberationPreinscription);
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

        return $this->render('deliberation_preinscription/edit.html.twig', [
            'deliberation_preinscription' => $deliberationPreinscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_deliberation_preinscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, DeliberationPreinscription $deliberationPreinscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_deliberation_preinscription_delete',
                    [
                        'id' => $deliberationPreinscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($deliberationPreinscription);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_deliberation_preinscription_index');

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

        return $this->render('deliberation_preinscription/delete.html.twig', [
            'deliberation_preinscription' => $deliberationPreinscription,
            'form' => $form,
        ]);
    }
}
