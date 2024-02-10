<?php

namespace App\Controller\Comptabilite;

use App\Entity\InfoPreinscription;
use App\Entity\NiveauEtudiant;
use App\Entity\Paiement;
use App\Entity\Preinscription;
use App\Form\NiveauEtudiantType;
use App\Form\PreinscriptionPaiementType;
use App\Form\PreinscriptionType;
use App\Repository\InfoPreinscriptionRepository;
use App\Repository\NaturePaiementRepository;
use App\Repository\NiveauEtudiantRepository;
use App\Repository\PaiementRepository;
use App\Repository\PreinscriptionRepository;
use App\Repository\UtilisateurGroupeRepository;
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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Registry;

#[Route('/comptabilite/niveau/etudiant')]
class NiveauEtudiantController extends AbstractController
{
    private $workflow;
    private $security;
    /**
     * Undocumented variable
     *
     * @var \App\Entity\Utilisateur
     */
    private $user;

    private $utilisateur;

    public function __construct(Security $security, Registry $workflow)
    {
        $this->workflow = $workflow;
        $this->security = $security;
        $this->user = $security->getUser()->getPersonne();
        $this->utilisateur = $security->getUser();
    }

    #[Route('/{etat}/{id}', name: 'app_comptabilite_niveau_etudiant_preinscription_suivi_formation_index', methods: ['GET', 'POST'])]
    public function indexFormation(Request $request, DataTableFactory $dataTableFactory, $etat, $id, UtilisateurGroupeRepository $utilisateurGroupeRepository): Response
    {
        // dd($etat);
        $titre = '';
        if ($etat == "attente_paiement") {
            $titre = "Liste des préinscriptions en attente de finalisation";
        } elseif ($etat == "valider") {
            $titre = "Liste des préinscriptions payées";
        } elseif ($etat == "attente_validation") {
            $titre = "Liste des préinscriptions en attente de validation";
        } else {
            $titre = "Liste des préinscriptions en attente de confirmation";
        }

        if ($etat == "valider_non_paye" || $etat == "valider_paye") {
            $table = $dataTableFactory->create()
                //->add('nom', TextColumn::class, ['field' => 'etudiant.getNomComplet', 'label' => 'Nom et Prénoms'])
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                    return   $preinscription->getEtudiant()->getNomComplet();
                }])
                ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de demande', 'format' => 'd-m-Y', 'searchable' => false])
                ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
                /*   ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle']) */
                /* ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
                ->add('montant', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.', 'field' => 'filiere.montantPreinscription'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Preinscription::class,
                    'query' => function (QueryBuilder $qb) use ($etat, $utilisateurGroupeRepository, $id) {
                        $qb->select('e, filiere, etudiant,niveau')
                            ->from(Preinscription::class, 'e')
                            ->join('e.etudiant', 'etudiant')
                            /*   ->join('e.filiere', 'filiere') */
                            /*  ->leftJoin('e.caissiere', 'c') */
                            ->join('e.niveau', 'niveau')
                            ->join('niveau.filiere', 'filiere')

                            ->andWhere('e.id = :id')
                            ->andWhere('e.etat = :statut')
                            ->setParameter('id', $id)
                            ->setParameter('statut', $etat);

                        if ($utilisateurGroupeRepository->findOneBy(array('utilisateur' => $this->utilisateur))->getGroupe()->getLibelle() == "Etudiants") {
                            $qb->andWhere('etudiant.id = :etudiant')
                                ->setParameter('etudiant', $this->user->getId());
                        } else {
                            $qb
                                ->andWhere('etudiant.etat = :etat')
                                ->setParameter('etat', 'complete');
                        }
                    }
                ])
                ->setName('dt_app_comptabilite_niveau_etudiant_preinscription_suivi_formation_' . $etat);
        } else {
            $table = $dataTableFactory->create()
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                    return   $preinscription->getEtudiant()->getNomComplet();
                }])
                ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de demande', 'format' => 'd-m-Y', 'searchable' => false])
                ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
                /*   ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle']) */
                ->add('montant', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.', 'field' => 'filiere.montantPreinscription'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Preinscription::class,
                    'query' => function (QueryBuilder $qb) use ($etat, $utilisateurGroupeRepository, $id) {
                        $qb->select('e, filiere, etudiant,niveau')
                            ->from(Preinscription::class, 'e')
                            ->join('e.etudiant', 'etudiant')
                            /*   ->join('e.filiere', 'filiere') */
                            ->join('e.niveau', 'niveau')
                            ->join('niveau.filiere', 'filiere')

                            ->andWhere('e.id = :id')
                            ->andWhere('e.etat = :statut')
                            ->setParameter('id', $id)
                            ->setParameter('statut', $etat);

                        if ($utilisateurGroupeRepository->findOneBy(array('utilisateur' => $this->utilisateur))->getGroupe()->getLibelle() == "Etudiants") {
                            $qb->andWhere('etudiant.id = :etudiant')
                                ->setParameter('etudiant', $this->user->getId());
                        } else {
                            $qb
                                ->andWhere('etudiant.etat = :etat')
                                ->setParameter('etat', 'complete');
                        }
                    }
                ])
                ->setName('dt_app_comptabilite_niveau_etudiant_preinscription_suivi_formation' . $etat . $id);
        }



        $renders = [
            'edit' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'valider_non_paye') {
                    return true;
                } else {
                    return false;
                }
            }),
            'verification' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'attente_validation') {
                    return true;
                } else {
                    return false;
                }
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Preinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'verification' => [
                                'target' => '#modal-xl',
                                'url' => $this->generateUrl('verification_validation_dossier', ['id' => $context->getEtudiant()->getId(), 'preinscription' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['verification']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_payer', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_delete', ['id' => $value]),
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


        return $this->render('etudiant/preinscription/index_suivi_formation.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'id' => $id,
            'titre' => $titre,
        ]);
    }



    #[Route('/{etat}', name: 'app_comptabilite_niveau_etudiant_preinscription_index', methods: ['GET', 'POST'])]
    public function indexPreinscription(Request $request, DataTableFactory $dataTableFactory, $etat, UtilisateurGroupeRepository $utilisateurGroupeRepository): Response
    {
        // dd($etat);
        $titre = '';
        if ($etat == "attente_paiement") {
            $titre = "Liste des préinscriptions en attente de finalisation";
        } elseif ($etat == "valider") {
            $titre = "Liste des préinscriptions payées";
        } elseif ($etat == "attente_validation") {
            $titre = "Liste des préinscriptions en attente de validation";
        } else {
            $titre = "Liste des préinscriptions en attente de confirmation";
        }

        if ($etat == "valider_non_paye" || $etat == "valider_paye") {
            $table = $dataTableFactory->create()
                //->add('nom', TextColumn::class, ['field' => 'etudiant.getNomComplet', 'label' => 'Nom et Prénoms'])
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                    return   $preinscription->getEtudiant()->getNomComplet();
                }])
                ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de demande', 'format' => 'd-m-Y', 'searchable' => false])
                ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
                /*   ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle']) */
                /* ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
                ->add('montant', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.', 'field' => 'filiere.montantPreinscription'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Preinscription::class,
                    'query' => function (QueryBuilder $qb) use ($etat, $utilisateurGroupeRepository) {
                        $qb->select('e, filiere, etudiant,niveau')
                            ->from(Preinscription::class, 'e')
                            ->join('e.etudiant', 'etudiant')
                            /*   ->join('e.filiere', 'filiere') */
                            /*  ->leftJoin('e.caissiere', 'c') */
                            ->join('e.niveau', 'niveau')
                            ->join('niveau.filiere', 'filiere')

                            ->andWhere('e.etat = :statut')
                            ->setParameter('statut', $etat);

                        if ($utilisateurGroupeRepository->findOneBy(array('utilisateur' => $this->utilisateur))->getGroupe()->getLibelle() == "Etudiants") {
                            $qb->andWhere('etudiant.id = :etudiant')
                                ->setParameter('etudiant', $this->user->getId());
                        } else {
                            $qb
                                ->andWhere('etudiant.etat = :etat')
                                ->setParameter('etat', 'complete');
                        }
                    }
                ])
                ->setName('dt_app_comptabilite_niveau_etudiant_preinscription' . $etat);
        } else {
            $table = $dataTableFactory->create()
                ->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                    return   $preinscription->getEtudiant()->getNomComplet();
                }])
                ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date de demande', 'format' => 'd-m-Y', 'searchable' => false])
                ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
                /*   ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle']) */
                ->add('montant', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.', 'field' => 'filiere.montantPreinscription'])
                ->createAdapter(ORMAdapter::class, [
                    'entity' => Preinscription::class,
                    'query' => function (QueryBuilder $qb) use ($etat, $utilisateurGroupeRepository) {
                        $qb->select('e, filiere, etudiant,niveau')
                            ->from(Preinscription::class, 'e')
                            ->join('e.etudiant', 'etudiant')
                            /*   ->join('e.filiere', 'filiere') */
                            ->join('e.niveau', 'niveau')
                            ->join('niveau.filiere', 'filiere')

                            ->andWhere('e.etat = :statut')
                            ->setParameter('statut', $etat);

                        if ($utilisateurGroupeRepository->findOneBy(array('utilisateur' => $this->utilisateur))->getGroupe()->getLibelle() == "Etudiants") {
                            $qb->andWhere('etudiant.id = :etudiant')
                                ->setParameter('etudiant', $this->user->getId());
                        } else {
                            $qb
                                ->andWhere('etudiant.etat = :etat')
                                ->setParameter('etat', 'complete');
                        }
                    }
                ])
                ->setName('dt_app_comptabilite_niveau_etudiant_preinscription' . $etat);
        }



        $renders = [
            'edit' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'valider_non_paye') {
                    return true;
                } else {
                    return false;
                }
            }),
            'verification' =>  new ActionRender(function () use ($etat) {
                if ($etat == 'attente_validation') {
                    return true;
                } else {
                    return false;
                }
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Preinscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'verification' => [
                                'target' => '#modal-xl',
                                'url' => $this->generateUrl('verification_validation_dossier', ['id' => $context->getEtudiant()->getId(), 'preinscription' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['verification']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_payer', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_delete', ['id' => $value]),
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


        return $this->render('etudiant/preinscription/index.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'titre' => $titre,
        ]);
    }

    #[Route('/', name: 'app_comptabilite_niveau_etudiant_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, UserInterface $user): Response
    {
        $ver = $this->isGranted('ROLE_ETUDIANT');
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code Preinscription'])
            ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                return   $preinscription->getEtudiant()->getNomComplet();
            }])
            /* ->add('etudiant', TextColumn::class, ['field' => 'etudiant.nom', 'label' => 'Nom'])
            ->add('prenoms', TextColumn::class, ['field' => 'etudiant.prenom', 'label' => 'Prénoms']) */
            ->add('etudiant.dateNaissance', DateTimeColumn::class, ['label' => 'Date de naissance', 'format' => 'd-m-Y', "searchable" => false, 'field' => 'etudiant.dateNaissance'])
            ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date pré-inscription', 'format' => 'd/m/Y', "searchable" => false,])
            /*   ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
            //->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Preinscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $ver) {
                    $qb->select('e, filiere, etudiant,niveau,c')
                        ->from(Preinscription::class, 'e')
                        ->join('e.etudiant', 'etudiant')
                        ->join('e.niveau', 'niveau')
                        ->join('niveau.filiere', 'filiere')
                        ->leftJoin('e.caissiere', 'c')
                        ->andWhere('e.etat = :statut')
                        ->setParameter('statut', 'attente_paiement');

                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('e.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }
                }
            ])
            ->setName('dt_app_comptabilite_niveau_etudiant');
        // dd($this->isGranted('ROLE_ETUDIANT'));
        $renders = [
            'edit' => new ActionRender(fn () => $ver == false),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'show' => new ActionRender(function () {
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
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_paiement_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilite_preinscription_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_delete', ['id' => $value]),
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


        return $this->render('comptabilite/niveau_etudiant/index.html.twig', [
            'datatable' => $table
        ]);
    }
    #[Route('/suivi/formation/{id}/paiement', name: 'app_comptabilite_niveau_etudiant_formation_index', methods: ['GET', 'POST'])]
    public function indexFormationSuivi(Request $request, DataTableFactory $dataTableFactory, UserInterface $user, $id): Response
    {
        $ver = $this->isGranted('ROLE_ETUDIANT');
        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code Preinscription'])
            ->add('etudiant', TextColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, Preinscription $preinscription) {
                return   $preinscription->getEtudiant()->getNomComplet();
            }])
            /* ->add('etudiant', TextColumn::class, ['field' => 'etudiant.nom', 'label' => 'Nom'])
            ->add('prenoms', TextColumn::class, ['field' => 'etudiant.prenom', 'label' => 'Prénoms']) */
            ->add('etudiant.dateNaissance', DateTimeColumn::class, ['label' => 'Date de naissance', 'format' => 'd-m-Y', "searchable" => false, 'field' => 'etudiant.dateNaissance'])
            ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
            ->add('datePreinscription', DateTimeColumn::class, ['label' => 'Date pré-inscription', 'format' => 'd/m/Y', "searchable" => false,])
            /*   ->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']) */
            //->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Preinscription::class,
                'query' => function (QueryBuilder $qb) use ($user, $id) {
                    $qb->select('e, filiere, etudiant,niveau,c')
                        ->from(Preinscription::class, 'e')
                        ->join('e.etudiant', 'etudiant')
                        ->join('e.niveau', 'niveau')
                        ->join('niveau.filiere', 'filiere')
                        ->leftJoin('e.caissiere', 'c')
                        ->andWhere('e.id = :id')
                        ->andWhere('e.etat = :statut')
                        ->setParameter('id', $id)
                        ->setParameter('statut', 'attente_paiement');

                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('e.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }
                }
            ])
            ->setName('dt_app_comptabilite_niveau_etudiant_formation' . $id);
        // dd($this->isGranted('ROLE_ETUDIANT'));
        $renders = [
            'edit' => new ActionRender(fn () => $ver == false),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'show' => new ActionRender(function () {
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
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_paiement_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['edit']
                            ],
                            'show' => [
                                'url' => $this->generateUrl('app_comptabilite_preinscription_show', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-eye',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['show']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_delete', ['id' => $value]),
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


        return $this->render('comptabilite/niveau_etudiant/index.html.twig', [
            'datatable' => $table,
            'id' => $id
        ]);
    }

    #[Route('/{etat}', name: 'app_comptabilite_niveau_etudiant_valider_index', methods: ['GET', 'POST'])]
    public function indexValider(Request $request, $etat, DataTableFactory $dataTableFactory, Security $security): Response
    {


        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('etudiant', NumberFormatColumn::class, ['label' => 'Nom et Prénoms', 'render' => function ($value, NiveauEtudiant $preinscription) {
                return   $preinscription->getEtudiant()->getNomComplet();
            }])
            ->add('dateNaissance', DateTimeColumn::class, ['label' => 'Date de naissance', 'format' => 'd-m-Y', 'field' => 'etudiant.dateNaissance', 'searchable' => false])
            ->add('filiere', TextColumn::class, ['label' => 'Filiere', 'field' => 'filiere.libelle'])
            ->add('date', DateTimeColumn::class, ['label' => 'Date de demande', 'format' => 'd-m-Y',])
            ->add('dateValidation', DateTimeColumn::class, ['label' => 'Date de validation', 'format' => 'd-m-Y',])
            //->add('montantPreinscription', NumberFormatColumn::class, ['label' => 'Mnt. Préinscr.'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => NiveauEtudiant::class,
                'query' => function (QueryBuilder $qb) use ($etat) {
                    $qb->select('e, filiere, etudiant')
                        ->from(NiveauEtudiant::class, 'e')
                        ->join('e.etudiant', 'etudiant')
                        ->join('e.filiere', 'filiere')
                        ->andWhere('e.etat = :statut')
                        ->setParameter('statut', $etat);
                }
            ])
            ->setName('dt_app_comptabilite_niveau_etudiant_valider' . $etat);

        $renders = [
            'edit' =>  new ActionRender(function () use ($etat) {
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, NiveauEtudiant $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_payer', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            /* 'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_comptabilite_niveau_etudiant_delete', ['id' => $value]),
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


        return $this->render('comptabilite/niveau_etudiant/index_valider.html.twig', [
            'datatable' => $table,
            'etat' => $etat
        ]);
    }


    #[Route('/new', name: 'app_comptabilite_niveau_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $niveauEtudiant = new NiveauEtudiant();
        $form = $this->createForm(NiveauEtudiantType::class, $niveauEtudiant, [
            'method' => 'POST',
            'etat' => 'autre',
            'action' => $this->generateUrl('app_comptabilite_niveau_etudiant_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_niveau_etudiant_index');




            if ($form->isValid()) {

                $niveauEtudiant->setEtat('attente_validation');
                $entityManager->persist($niveauEtudiant);
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

        return $this->render('comptabilite/niveau_etudiant/new.html.twig', [
            'niveau_etudiant' => $niveauEtudiant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_niveau_etudiant_show', methods: ['GET'])]
    public function show(NiveauEtudiant $niveauEtudiant): Response
    {
        return $this->render('comptabilite/niveau_etudiant/show.html.twig', [
            'niveau_etudiant' => $niveauEtudiant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_paiement_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Preinscription  $preinscription, PaiementRepository $paiementRepository, InfoPreinscriptionRepository $infoPreinscriptionRepository, PreinscriptionRepository $preinscriptionRepository, EntityManagerInterface $entityManager, FormError $formError, NaturePaiementRepository $naturePaiementRepository): Response
    {
        // dd($niveauEtudiant->getEtudiant()->getNom());

        //dd('ffff');
        $form = $this->createForm(PreinscriptionPaiementType::class, $preinscription, [
            'method' => 'POST',
            'etat' => $preinscription->getEtat(),
            'action' => $this->generateUrl('app_comptabilite_paiement_etudiant_edit', [
                'id' =>  $preinscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($preinscription->getEtat() == "paiement_confirmation") {
            $form->get('datePaiement')->setData($preinscription->getInfoPreinscription()->getDatePaiement());
            $form->get('modePaiement')->setData($preinscription->getInfoPreinscription()->getModePaiement());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_niveau_etudiant_index');

            $workflow = $this->workflow->get($preinscription, 'preinscription');

            $mode = $naturePaiementRepository->find($form->get('modePaiement')->getData()->getId());
            //dd();
            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'payer') {

                    if (!$mode->isConfirmation()) {
                        $workflow->apply($preinscription, 'paiement');
                    } else {
                        $workflow->apply($preinscription, 'paiement_confirmation');
                    }
                    $paiement = new Paiement();
                    $paiement->setDatePaiement($form->get('datePaiement')->getData());
                    //$paiement->setPreinscription($preinscription);
                    $paiement->setUtilisateur($this->getUser());
                    $paiement->setReference($preinscription->getCode());

                    $paiementRepository->add($paiement, true);

                    $infos = new InfoPreinscription();
                    $infos->setUtilisateur($this->getUser());
                    $infos->setMontant($preinscription->getNiveau()->getFiliere()->getMontantPreinscription());
                    $infos->setDatePaiement($form->get('datePaiement')->getData());
                    $infos->setPreinscription($preinscription);
                    $infos->setModePaiement($form->get('modePaiement')->getData());
                    $infoPreinscriptionRepository->add($infos, true);
                    // $niveauEtudiant->setCode($niveauEtudiant->getFiliere()->getCode())
                    //  $preinscription->setDatePaiement($form->get('datePaiement')->getData());;
                    $preinscriptionRepository->add($preinscription, true);
                } elseif ($form->getClickedButton()->getName() === 'confirmation') {

                    $preinscription->setEtat('valide');
                    $preinscriptionRepository->add($preinscription, true);
                } else {
                    $preinscriptionRepository->add($preinscription, true);
                }

                /* $entityManager->persist($niveauEtudiant);
                $entityManager->flush();*/
                $preinscription->setCaissiere($this->getUser());
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

        return $this->render('comptabilite/niveau_etudiant/edit.html.twig', [
            'preinscription' => $preinscription,
            'form' => $form->createView(),
            'etat' => $preinscription->getEtat(),
            'etudiant' => $preinscription->getEtudiant(),
        ]);
    }

    #[Route('/{id}/rejeter', name: 'app_comptabilite_niveau_etudiant_rejeter', methods: ['GET', 'POST'])]
    public function rejeter(Request $request, NiveauEtudiant $niveauEtudiant, NiveauEtudiantRepository $niveauEtudiantRepositoryn, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(NiveauEtudiantType::class, $niveauEtudiant, [
            'method' => 'POST',
            'etat' => 'rejeter',
            'action' => $this->generateUrl('app_comptabilite_niveau_etudiant_rejeter', [
                'id' =>  $niveauEtudiant->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_niveau_etudiant_index');

            $workflow = $this->workflow->get($niveauEtudiant, 'niveau_etudiant');



            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'rejeter') {
                    $workflow->apply($niveauEtudiant, 'rejet');
                    $niveauEtudiant->setDateValidation(new \DateTime());
                    $niveauEtudiantRepositoryn->add($niveauEtudiant, true);
                } else {
                    $niveauEtudiantRepositoryn->add($niveauEtudiant, true);
                }

                /* $entityManager->persist($niveauEtudiant);
                 $entityManager->flush();*/

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

        return $this->render('comptabilite/niveau_etudiant/rejeter.html.twig', [
            'niveau_etudiant' => $niveauEtudiant,
            'form' => $form->createView(),
            'etudiant' => $niveauEtudiant->getEtudiant(),
        ]);
    }

    #[Route('/{id}/payer', name: 'app_comptabilite_niveau_etudiant_payer', methods: ['GET', 'POST'])]
    public function payer(Request $request, NiveauEtudiant $niveauEtudiant, NiveauEtudiantRepository $niveauEtudiantRepositoryn, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(NiveauEtudiantType::class, $niveauEtudiant, [
            'method' => 'POST',
            'etat' => 'payer',
            'action' => $this->generateUrl('app_comptabilite_niveau_etudiant_payer', [
                'id' =>  $niveauEtudiant->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_niveau_etudiant_index');

            $workflow = $this->workflow->get($niveauEtudiant, 'attente_validation');



            if ($form->isValid()) {

                if ($form->getClickedButton()->getName() === 'payer') {
                    $workflow->apply($niveauEtudiant, 'valide');

                    $niveauEtudiantRepositoryn->add($niveauEtudiant, true);
                } else {
                    $niveauEtudiantRepositoryn->add($niveauEtudiant, true);
                }

                /* $entityManager->persist($niveauEtudiant);
                 $entityManager->flush();*/

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

        return $this->render('comptabilite/niveau_etudiant/payer.html.twig', [
            'niveau_etudiant' => $niveauEtudiant,
            'form' => $form->createView(),
            'etudiant' => $niveauEtudiant->getEtudiant(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_niveau_etudiant_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, NiveauEtudiant $niveauEtudiant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_niveau_etudiant_delete',
                    [
                        'id' => $niveauEtudiant->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($niveauEtudiant);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_comptabilite_niveau_etudiant_index');

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

        return $this->render('comptabilite/niveau_etudiant/delete.html.twig', [
            'niveau_etudiant' => $niveauEtudiant,
            'form' => $form,
        ]);
    }
}
