<?php

namespace App\Controller\Comptabilite;

use App\Entity\InfoPreinscription;
use App\Entity\Preinscription;
use App\Entity\Validation;
use App\Form\InfoPreinscriptionType;
use App\Form\PreinscriptionEudiantConnecteType;
use App\Form\PreinscriptionType;
use App\Repository\NiveauRepository;
use App\Repository\PreinscriptionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\MapColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/admin/comptabilite/preinscription')]
class PreinscriptionController extends AbstractController
{

    private $em;
    public function __construct(private WorkflowInterface $preinscriptionStateMachine, EntityManagerInterface $em)
    {
        /*$etats = [
            'attente_validation' => 'En attente de'
        ]  */

        $this->em = $em;
    }


    #[Route('/', name: 'app_comptabilite_preinscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserInterface $user, DataTableFactory $dataTableFactory): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $table = $dataTableFactory->create()
            ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de la demande', 'format' => 'd-m-Y'])
            ->add('etat', MapColumn::class, ['label' => 'Etat', 'map' => Preinscription::ETATS]);

        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Preinscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
        }

        $table->createAdapter(ORMAdapter::class, [
            'entity' => Preinscription::class,
            'query' => function (QueryBuilder $qb) use ($user) {
                $qb->select(['p', 'niveau', 'filiere', 'etudiant'])
                    ->from(Preinscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
            }
        ])
            ->setName('dt_app_comptabilite_preinscription_user');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Preinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            /* 'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_preinscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_comptabilite_preinscription_delete', ['id' => $value]),
                            'ajax' => true,
                            'stacked' => false,
                            'icon' => '%icon% bi bi-trash',
                            'attrs' => ['class' => 'btn-danger'],
                            'render' => $renders['delete']
                        ]*/]

                    ];
                    return $this->renderView('_includes/default_actions.html.twig', compact('options', 'context'));
                }
            ]);
        }


        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }


        return $this->render('comptabilite/preinscription/index.html.twig', [
            'datatable' => $table
        ]);
    }



    #[Route('/{etat}/liste', name: 'app_comptabilite_preinscription_ls', methods: ['GET', 'POST'])]
    public function liste(Request $request, UserInterface $user, string $etat, DataTableFactory $dataTableFactory): Response
    {

        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de la demande', 'format' => 'd-m-Y'])

            ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);


        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Preinscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
        }


        if ($etat == 'valide') {
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']);
            $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'info.datePaiement', 'format' => 'd-m-Y']);
        }
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Preinscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant', 'info'])
                    ->from(Preinscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.infoPreinscription', 'info')
                    ->leftJoin('p.caissiere', 'c')
                    ->andWhere('p.etat = :etat')
                    ->setParameter('etat', $etat);
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
            }
        ])
            ->setName('dt_app_comptabilite_preinscription_' . $etat);

        $renders = [
            'edit' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'paiement_confirmation') {
                    return true;
                } else {
                    return false;
                }
            }),   'imprime' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'valide') {
                    return true;
                } else {
                    return false;
                }
            }),
            'delete' => new ActionRender(function () use ($etat) {
                if ($etat == 'paiement_confirmation') {
                    return true;
                } else {
                    return false;
                }
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Preinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'imprime' => [
                                'url' => $this->generateUrl('app_comptabilite_print', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% fa fa-print',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Imprime'],
                                'render' => new ActionRender(fn () => $context->getEtat() == 'valide')
                                //'render' => $renders['imprime']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_preinscription_validate', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-check',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => new ActionRender(fn () => $context->getEtat() == 'paiement_confirmation')
                            ],
                            'paiement' => [
                                'url' => $this->generateUrl('app_comptabilite_paiement_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning', 'title' => 'Paiements'],
                                'render' => new ActionRender(fn () => $context->getEtat() == 'paiement_confirmation')
                            ],
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


        return $this->render('comptabilite/preinscription/liste.html.twig', [
            'datatable' => $table,
            'etat' => $etat
        ]);
    }


    #[Route('/new', name: 'app_comptabilite_preinscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserInterface $user, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $preinscription = new Preinscription();
        $preinscription->setEtudiant($user->getPersonne());
        $form = $this->createForm(PreinscriptionType::class, $preinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_preinscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $showAlert = false;
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_preinscription_index');




            if ($form->isValid()) {
                $this->preinscriptionStateMachine->getMarking($preinscription);
                $entityManager->persist($preinscription);
                $entityManager->flush();

                $data = true;
                $showAlert = true;
                $message       = sprintf('Votre demande pour le niveau [%s] a été enregistrée. Elle sera traitée par nos services pour la suite de votre parcours', $preinscription->getNiveau()->getLibelle());
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('comptabilite/preinscription/new.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
        ]);
    }

    private function numero($code)
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Preinscription::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ($code . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route('/demande/new', name: 'app_comptabilite_preinscription_demande_new', methods: ['GET', 'POST'])]
    public function demanddNew(Request $request, NiveauRepository $niveauRepository, UserInterface $user, EntityManagerInterface $entityManager, FormError $formError, PreinscriptionRepository $preinscriptionRepository): Response
    {
        $preinscription = new Preinscription();
        //dd();
        $form = $this->createForm(PreinscriptionEudiantConnecteType::class, $preinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_preinscription_demande_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $showAlert = false;
        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_preinscription_index');




            if ($form->isValid()) {
                $preinscription->setDatePreinscription(new \DateTime());
                $preinscription->setEtudiant($this->getUser()->getPersonne());
                $preinscription->setUtilisateur($this->getUser());
                $preinscription->setCode($this->numero($niveauRepository->find($form->get('niveau')->getData()->getId())->getCode()));
                $preinscription->setEtat('attente_paiement');
                $entityManager->persist($preinscription);
                $entityManager->flush();

                $data = true;
                $showAlert = true;
                $message       = sprintf('Votre demande pour le niveau [%s] a été enregistrée. Elle sera traitée par nos services pour la suite de votre parcours', $preinscription->getNiveau()->getLibelle());
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('comptabilite/preinscription/demande_new.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
            'info' => $preinscriptionRepository->getLastRecord()[0]
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_preinscription_show', methods: ['GET'])]
    public function show(Preinscription $preinscription): Response
    {
        return $this->render('comptabilite/preinscription/show.html.twig', [
            'preinscription' => $preinscription,
        ]);
    }


    #[Route('/{id}/paiement', name: 'app_comptabilite_preinscription_paiement', methods: ['GET', 'POST'])]
    public function paiement(Request $request, Preinscription $preinscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $infoPreinscription = new InfoPreinscription();
        $infoPreinscription->setPreinscription($preinscription);
        $infoPreinscription->setMontant($preinscription->getFiliere()->getMontantPreinscription());
        $form = $this->createForm(InfoPreinscriptionType::class, $infoPreinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_preinscription_paiement', [
                'id' =>  $preinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_preinscription_index');




            if ($form->isValid()) {
                $this->preinscriptionStateMachine->apply($preinscription, 'paiement');
                $entityManager->persist($infoPreinscription);
                $entityManager->persist($preinscription);
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

        return $this->render('comptabilite/preinscription/paiement.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_preinscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Preinscription $preinscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(PreinscriptionType::class, $preinscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_preinscription_edit', [
                'id' =>  $preinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_preinscription_index');




            if ($form->isValid()) {

                $entityManager->persist($preinscription);
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

        return $this->render('comptabilite/preinscription/edit.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}/validate', name: 'app_comptabilite_preinscription_validate', methods: ['GET', 'POST', 'PATCH'])]
    public function validate(Request $request, Preinscription $preinscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $validation = new Validation();
        $validation->setObject($preinscription->getId());
        $validation->setClassName(get_class($preinscription));
        $form = $this->createForm(PreinscriptionType::class, $preinscription, [
            'method' => 'PATCH',
            'validate' => true,
            'action' => $this->generateUrl('app_comptabilite_preinscription_validate', [
                'id' =>  $preinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_preinscription_index');




            if ($form->isValid()) {
                $validation->setEtat($preinscription->getEtat());
                $validation->setCommentaire($preinscription->getMotif() ?: '');
                $entityManager->persist($validation);
                $entityManager->persist($preinscription);
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

        return $this->render('comptabilite/preinscription/edit.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_preinscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Preinscription $preinscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_preinscription_delete',
                    [
                        'id' => $preinscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($preinscription);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilite_preinscription_index');

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

        return $this->render('comptabilite/preinscription/delete.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form,
        ]);
    }
}
