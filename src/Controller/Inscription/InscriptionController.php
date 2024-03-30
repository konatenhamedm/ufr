<?php

namespace App\Controller\Inscription;

use App\Controller\FileTrait;
use App\Entity\InfoInscription;
use App\Entity\InfoPreinscription;
use App\Entity\Inscription;
use App\Entity\Paiement;
use App\Entity\Preinscription;
use App\Form\InscriptionPayementType;
use App\Form\InscriptionRejeterType;
use App\Form\InscriptionType;
use App\Repository\EcheancierRepository;
use App\Repository\FraisInscriptionRepository;
use App\Repository\FraisRepository;
use App\Repository\InfoPreinscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NaturePaiementRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Omines\Column\NumberFormatColumn;
use DeepCopy\Filter\Filter;
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
use Symfony\Component\Workflow\Registry;

#[Route('/admin/inscription/inscription')]
class InscriptionController extends AbstractController
{
    protected $workflow;
    use FileTrait;


    public function __construct(Registry $workflow)
    {

        $this->workflow = $workflow;
    }

    #[Route('/{id}/imprime', name: 'app_comptabilite_inscription_print', methods: ['GET'])]
    public function imprimer($id, Inscription $inscription, EcheancierRepository $echeancierRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu.html.twig", [
            'data' => $inscription,
            'info' => $echeancierRepository->findAllEcheanceDateFirst($id),
            'nombre' => count($inscription->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'p',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }
    #[Route('/{id}/imprime/attente_confirmation', name: 'app_comptabilite_inscription_print_attente_confirmation', methods: ['GET'])]
    public function imprimeAttenteConfirmation($id, Inscription $inscription, EcheancierRepository $echeancierRepository): Response
    {

        $imgFiligrame = "uploads/" . 'media_etudiant' . "/" . 'test.png';
        return $this->renderPdf("inscription/inscription/recu_attente_confirmation.html.twig", [
            'data' => $inscription,
            'info' => $echeancierRepository->findAllEcheanceDateFirst($id),
            'nombre' => count($inscription->getEcheanciers()),
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
            'protected' => true,

            'format' => 'A4',

            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ],
            'watermarkImg' =>  $imgFiligrame,
            'entreprise' => ''
        ], true);
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/{etat}/{id}', name: 'app_inscription_inscription_suivi_formation_index', methods: ['GET', 'POST'])]
    public function indexSuviFormation(Request $request, UserInterface $user, $id, string $etat, DataTableFactory $dataTableFactory): Response
    {

        //dd($user->getPersonne());

        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $titre = '';
        if ($etat == "echeance_soumis") {
            $titre = "Liste des inscriptions | validation des échéances";
        } elseif ($etat == "valide") {
            $titre = "Liste des inscriptions en attente de paiement";
        } elseif ($etat == "rejete") {
            $titre = "Liste des inscriptions rejettées | en attente d'écheancier";
        } elseif ($etat == 'attente_echeance') {
            $titre = "Liste des inscriptions en attente d'écheancier";
        } else {
            $titre = "Liste des inscriptions soldées";
        }
        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde


        if ($etat == 'valide') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
        } else {

            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);
            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.montant']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return    abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
        }



        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat, $id) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c')
                    ->andWhere('p.id = :id')
                    ->setParameter('id', $id);
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->andWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_suivi_formation' . $etat  . $id);

        $renders = [
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeance' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'confirmation' => new ActionRender(function () use ($etat) {
                return $etat == 'valide';
            }),
            'payer' => new ActionRender(fn () => $etat == 'valide' && $isEtudiant == false),

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'confirmation' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-list',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['payer']
                            ],
                            'payer' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],
                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
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


        return $this->render('inscription/inscription/index_suivi_formation.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'id' => $id,
            'titre' => $titre,
        ]);
    }





    #[Route('/formation', name: 'app_inscription_inscription_formation_index', methods: ['GET', 'POST'])]
    public function indexFormation(Request $request, UserInterface $user, DataTableFactory $dataTableFactory): Response
    {

        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');


        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde
        $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                return $preinscription->getEtudiant()->getNomComplet();
            }])
            ->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant'])
            ->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye'])
            ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
            }])
            ->add('etat', TextColumn::class, ['label' => 'Etape', 'render' => function ($value, Inscription $preinscription) {
                return   $preinscription->getEtat();
            }]);
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                /*   ->andWhere('p.etat = :etat')
                    ->setParameter('etat', $etat); */
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_formation_');

        $renders = [
            /*  'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeance' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'), */
            'delete' => new ActionRender(function () {
                return false;
            }),
            'suivi' => new ActionRender(function () {
                return true;
            }),
            /*  'payer' => new ActionRender(fn () => $etat == 'valide' && $isEtudiant == false), */

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [

                            'suivi' => [
                                'url' => $this->generateUrl('app_home_timeline_etudiant_formation_index', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-folder',
                                'attrs' => ['class' => 'btn-primary'],
                                'render' => $renders['suivi']
                            ],
                            /*  'payer' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_inscription_inscription_paiement', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ], */
                            /*  'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ], */
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
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


        return $this->render('inscription/inscription/index_formation.html.twig', [
            'datatable' => $table,
        ]);
    }


    #[Route('/{etat}', name: 'app_inscription_inscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, UserInterface $user, string $etat, DataTableFactory $dataTableFactory, EcheancierRepository $echeancierRepository): Response
    {

        //dd($etat);
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $titre = '';
        if ($etat == "echeance_soumis") {
            $titre = "Liste des inscriptions | validation des échéances";
        } elseif ($etat == "valide") {
            $titre = "Liste des inscriptions en attente de paiement";
        } elseif ($etat == "rejete") {
            $titre = "Liste des inscriptions rejettées | en attente d'écheancier";
        } elseif ($etat == 'attente_echeance') {
            $titre = "Liste des inscriptions en attente d'écheancier";
        } else {
            $titre = "Liste des inscriptions soldées";
        }
        //Code Etudiant, Filière, Nom et prénoms, Coût formation, Total payé, Solde


        if ($etat == 'valide') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return   abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }])
                ->add('nombre', NumberFormatColumn::class, ['label' => 'Nombre ACF', 'className' => 'text-center', 'render' => function ($value, Inscription $preinscription) {
                    return abs($preinscription->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                        return $ligne->getEtat() == 'attente_confirmation';
                    })->count());
                }]);
        } elseif ($etat == 'echeance_soumis') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);

            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }

            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            //$table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.totalPaye']);
            $table
                ->add('nombre', NumberFormatColumn::class, ['label' => 'Nombre échéances', 'render' => function ($value, Inscription $preinscription) {
                    return   count($preinscription->getEcheanciers());
                }])
                /*     ->add('dateDebut', TextColumn::class, ['label' => 'Nombre échéances', 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {
                    dd($echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['debut']);
                    return;
                }]); */

                ->add('debut', DateTimeColumn::class, ['label' => 'Date début', 'format' => 'd-m-Y', 'searchable' => false, 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {

                    return $echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['debut'];
                }])
                ->add('debutFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y', 'searchable' => false, 'render' => function ($value, Inscription $preinscription) use ($echeancierRepository) {

                    return $echeancierRepository->findAllEcheanceDateFirst($preinscription->getId())[0]['fin'];
                }]);
        } elseif ($etat == 'attente_echeance') {
            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);
            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
        } else {

            $table = $dataTableFactory->create()->add('code', TextColumn::class, ['label' => 'Code'])
                ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière']);
            if (!$isEtudiant) {
                $table->add('nom_prenom', TextColumn::class, ['label' => 'Nom et prénoms', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
            }
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Coût formation', 'field' => 'p.montant']);
            $table->add('totalPaye', NumberFormatColumn::class, ['label' => 'Total payé', 'field' => 'p.montant']);
            $table
                ->add('solde', NumberFormatColumn::class, ['label' => 'Solde', 'render' => function ($value, Inscription $preinscription) {
                    return    abs($preinscription->getMontant() - $preinscription->getTotalPaye());
                }]);
            /* ->add('nombre', TextColumn::class, ['label' => 'Nombre attente confirmation', 'render' => function ($value, Inscription $preinscription) {
                    return abs($preinscription->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                        return $ligne->getEtat() == 'attente_confirmation';
                    })->count());
                }]); */
        }



        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->andWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_' . $etat);

        $renders = [
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeance' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'recu' => new ActionRender(function () use ($etat) {
                if ($etat == 'solde') {
                    return true;
                } else {
                    return false;
                }
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'confirmation' => new ActionRender(function () use ($etat) {
                return $etat == 'valide';
            }),
            'payer' => new ActionRender(fn () => $etat == 'valide' && $isEtudiant == false),
            //'recu' => new ActionRender(fn () => $etat == 'solde'),

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-xl2',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack'],
                                'render' => $renders['recu']
                            ],
                            'imprime_recu_confirmation' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print_attente_confirmation',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-primary btn-stack'],
                                'render' => new ActionRender(fn () => $context->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                                    return $ligne->getEtat() == 'attente_confirmation';
                                })->count() >= 1 && $context->getEtat() == 'valide')
                            ],
                            'imprime_recu_confirmation_ok' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack'],
                                'render' => new ActionRender(fn () => $context->getInfoInscriptions()->filter(function (InfoInscription $ligne) {
                                    return $ligne->getEtat() == 'attente_confirmation';
                                })->count() == 0 && $context->getEtat() == 'valide')
                            ],

                            /*  'recu' => [
                                'target' => '#modal-lg',
                                'url' => $this->generateUrl('app_comptabilite_inscription_print', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => new ActionRender(fn() => !$context->getSortieCaisse() || $context->getSortieCaisse()->getEtat() == 'demande')
                            ], */
                            'confirmation' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_infoinscription_info_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-list',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['payer']
                            ],
                            'payer' => [
                                'target' => '#modal-xl2',
                                'url' => $this->generateUrl('app_config_paiement_inscription_index', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],
                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
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


        return $this->render('inscription/inscription/index_first.html.twig', [
            'datatable' => $table,
            'etat' => $etat,
            'titre' => $titre,
        ]);
    }
    #[Route('/{etat}', name: 'app_inscription_inscription_list_ls', methods: ['GET', 'POST'])]
    public function indexListe(Request $request, UserInterface $user, string $etat, DataTableFactory $dataTableFactory): Response
    {
        $isEtudiant = $this->isGranted('ROLE_ETUDIANT');

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('filiere', TextColumn::class, ['field' => 'filiere.libelle', 'label' => 'Filière'])
            ->add('niveau', TextColumn::class, ['field' => 'niveau.libelle', 'label' => 'Niveau'])
            ->add('dateInscription', DateTimeColumn::class, ['label' => 'Date création', 'format' => 'd-m-Y']);

        if ($etat != 'attente_echeancier') {
            $table->add('caissiere', TextColumn::class, ['field' => 'c.getNomComplet', 'label' => 'Caissière ']);
        }


        if (!$isEtudiant) {
            $table->add('nom', TextColumn::class, ['field' => 'etudiant.nom', 'visible' => false])
                ->add('prenom', TextColumn::class, ['field' => 'etudiant.prenom', 'visible' => false])
                ->add('nom_prenom', TextColumn::class, ['label' => 'Demandeur', 'render' => function ($value, Inscription $preinscription) {
                    return $preinscription->getEtudiant()->getNomComplet();
                }]);
        }
        if ($etat == 'valide') {
            $table->add('montant', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant']);
            $table->add('datePaiement', DateTimeColumn::class, ['label' => 'Date de paiement', 'field' => 'info.datePaiement', 'format' => 'd-m-Y']);
        }
        $table->createAdapter(ORMAdapter::class, [
            'entity' => Inscription::class,
            'query' => function (QueryBuilder $qb) use ($user, $etat) {
                $qb->select(['p', 'niveau', 'c', 'filiere', 'etudiant,res'])
                    ->from(Inscription::class, 'p')
                    ->join('p.niveau', 'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('niveau.responsable', 'res')
                    ->join('p.etudiant', 'etudiant')
                    ->leftJoin('p.caissiere', 'c');
                if ($this->isGranted('ROLE_ETUDIANT')) {
                    $qb->andWhere('p.etudiant = :etudiant')
                        ->setParameter('etudiant', $user->getPersonne());
                }
                if ($etat == 'attente_echeance') {
                    $qb->orWhere('p.etat = :etat')
                        ->orWhere('p.etat = :etat2')
                        ->setParameter('etat', 'attente_echeancier')
                        ->setParameter('etat2', 'rejete');
                } else {
                    $qb->andWhere('p.etat = :etat')
                        ->setParameter('etat', $etat);
                }
                if ($this->isGranted('ROLE_DIRECTEUR')) {
                    $qb->andWhere('res.id = :id')
                        ->setParameter('id', $user->getPersonne()->getId());
                }
            }
        ])
            ->setName('dt_app_inscription_inscription_list_' . $etat);

        $renders = [
            /* 'edit' =>  new ActionRender(function () {
                return true;
            }), */
            'edit_etudiant' => new ActionRender(fn () => $etat == 'attente_echeancier' || $etat == 'rejete'),
            'edit' => new ActionRender(fn () => $etat == 'echeance_soumis'),
            'payer' => new ActionRender(fn () => $etat == 'valide'),
            'delete' => new ActionRender(function () {
                return true;
            }),
            // 'recu' => new ActionRender(fn () => $etat == 'solde'),
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Inscription $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'recu' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_inscription_print',
                                    'params' => [
                                        'id' => $value,
                                    ]
                                ]),
                                'ajax' => true,
                                'target' =>  '#exampleModalSizeSm2',
                                'icon' => '%icon% bi bi-printer',
                                'attrs' => ['class' => 'btn-main btn-stack']
                                //, 'render' => new ActionRender(fn() => $source || $etat != 'cree')
                            ],
                            'payer' => [
                                'url' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['payer']
                            ],

                            'edit_etudiant' => [
                                'url' => $this->generateUrl('app_inscription_inscription_etudiant_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit_etudiant']
                            ],
                            'edit' => [
                                'url' => $this->generateUrl('app_inscription_inscription_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_inscription_inscription_delete', ['id' => $value]),
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

        return $this->render('inscription/inscription/index.html.twig', [
            'datatable' => $table,
            'etat' => $etat
        ]);
    }


    #[Route('/new', name: 'app_inscription_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_inscription_inscription_index');




            if ($form->isValid()) {

                $entityManager->persist($inscription);
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

        return $this->render('inscription/inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_inscription_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('inscription/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }
    #[Route('/{id}/show/confirmation', name: 'app_inscription_inscription_paiement_confirmation', methods: ['GET'])]
    public function showConfirmation(Inscription $inscription): Response
    {
        return $this->render('inscription/inscription/show_confirmation.html.twig', [
            'infoinscription' => $inscription->getInfoInscriptions(),
        ]);
    }

    #[Route('/{id}/edit/admin', name: 'app_inscription_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {
                $somme = 0;

                foreach ($ligne as $key => $value) {
                    $somme += $value->getMontant();
                }

                if ($inscription->getMontant() == $somme) {

                    if ($form->getClickedButton()->getName() === 'passer') {
                        $workflow_data->apply($inscription, 'valider');


                        $entityManager->persist($inscription);
                    } elseif ($form->getClickedButton()->getName() === 'rejeter') {
                        $workflow_data->apply($inscription, 'rejet');

                        $entityManager->persist($inscription);
                    }

                    $entityManager->persist($inscription);
                    $entityManager->flush();

                    $message       = sprintf('Opération effectuée avec succès');
                    $showAlert = false;
                    $statut = 1;
                    $this->addFlash('success', $message);
                } else {
                    $showAlert = true;
                    $statut = 0;

                    if ($inscription->getMontant() > $somme) {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est inferieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    } else {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est superieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    }

                    $this->addFlash('danger', $message);
                }


                $data = true;

                //$message       = 'Opération effectuée avec succès';

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

        return $this->render('inscription/inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'frais' => $fraisRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }
    #[Route('/{id}/edit/rejeter', name: 'app_inscription_inscription_rejeter', methods: ['GET', 'POST'])]
    public function editRejeter(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionRejeterType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_rejeter', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            //$ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {

                $inscription->setEtat('rejete');
                $entityManager->persist($inscription);
                $entityManager->flush();


                $data = true;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $fullRedirect = true;
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('inscription/inscription/rejeter.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/paiement/admin/ok', name: 'app_inscription_inscription_paiement_ok', methods: ['GET', 'POST'])]
    public function paiement(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository, FormError $formError, FraisInscriptionRepository $fraisRepository, EcheancierRepository $echeancierRepository, UserInterface $user, NaturePaiementRepository $naturePaiementRepository): Response
    {



        $form = $this->createForm(InscriptionPayementType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_paiement_ok', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            //$ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            $echeanciers = $echeancierRepository->findAllEcheance($inscription->getId());
            $date = $form->get('datePaiement')->getData();
            $mode =   $mode = $naturePaiementRepository->find($form->get('modePaiement')->getData()->getId());

            $montant = (int) $form->get('montant')->getData();

            //dd($inscription->getId());



            if ($form->isValid()) {

                $last_key = count($echeanciers);
                $i = 1;



                foreach ($echeanciers as $key => $echeancier) {

                    //  dd($montant, $echeancier->getMontant());
                    if ($montant >= (int)$echeancier->getMontant()) {

                        $paiement = new InfoInscription();

                        $paiement->setUtilisateur($this->getUser());
                        $paiement->setCode($inscription->getCode());
                        $paiement->setDateValidation(new \DateTime());
                        $paiement->setInscription($inscription);
                        $paiement->setDatePaiement($date);
                        $paiement->setCaissiere($this->getUser());
                        $paiement->setModePaiement($mode);
                        $paiement->setMontant($echeancier->getMontant());
                        $paiement->setEchenacier($echeancier);
                        if ($mode->getCode() == 'CHQ') {

                            $paiement->setNumeroCheque($form->get('numeroCheque')->getData());
                            $paiement->setBanque($form->get('banque')->getData());
                            $paiement->setTireur($form->get('tireur')->getData());
                            $paiement->setContact($form->get('contact')->getData());
                            $paiement->setDateCheque($form->get('dateCheque')->getData());
                        }


                        if ($mode->isConfirmation()) {

                            $paiement->setEtat('attente_confirmation');
                        } else {

                            $paiement->setEtat('payer');
                        }


                        $entityManager->persist($paiement);
                        $entityManager->flush();

                        if ($mode->isConfirmation()) {

                            $echeancier->setEtat('attente_confirmation');
                        } else {

                            $echeancier->setEtat('payer');
                        }
                        $entityManager->persist($echeancier);
                        $entityManager->flush();



                        $montant = $montant - $echeancier->getMontant();
                        if (!$mode->isConfirmation()) {

                            $inscription->setTotalPaye($inscription->getTotalPaye() + $echeancier->getMontant());
                        }

                        $entityManager->persist($inscription);
                        $entityManager->flush();

                        if ($i == $last_key) {

                            if ($montant >= 0) {

                                if ($inscription->getMontant() == $inscription->getTotalPaye()) {

                                    $inscription->setEtat('solde');
                                }
                            }
                        }

                        //$message       = sprintf('Opération effectuée avec succès');
                    }

                    $i++;
                }
                $message       = sprintf('Opération effectuée avec succès');
                if ($inscription->getMontant() == $inscription->getTotalPaye()) {
                    $statut = 1;
                } else {
                    $statut = 0;
                    $this->addFlash('success', $message);
                }

                $showAlert = true;
                $data = true;


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

        return $this->render('inscription/inscription/edit_paiement.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'echeanciers' => $echeancierRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }


    #[Route('/{id}/edit/etudiant', name: 'app_inscription_inscription_etudiant_edit', methods: ['GET', 'POST'])]
    public function editEtudiant(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, FormError $formError, FraisInscriptionRepository $fraisRepository): Response
    {

        // dd('');

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_etudiant_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_home_timeline_index');
            $ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            if ($form->isValid()) {
                $somme = 0;

                foreach ($ligne as $key => $value) {
                    $somme += $value->getMontant();
                }



                if ($inscription->getMontant() == $somme) {

                    if ($form->getClickedButton()->getName() === 'resoumettre') {
                        $workflow_data->apply($inscription, 'soumission');


                        $entityManager->persist($inscription);
                    } elseif ($form->getClickedButton()->getName() === 'retour') {
                        $workflow_data->apply($inscription, 'retour');

                        $entityManager->persist($inscription);
                    }

                    $entityManager->persist($inscription);
                    $entityManager->flush();

                    $message       = sprintf('Opération effectuée avec succès');
                    $showAlert = false;
                    $this->addFlash('success', $message);
                    $statut = 1;
                } else {
                    $showAlert = true;

                    if ($inscription->getMontant() > $somme) {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est inferieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    } else {
                        $message       = sprintf('Désolé votre opération à échoué car le montant total [%s] de votre échéancier est superieur [%s] mon total à payer', $somme, $inscription->getMontant());
                    }

                    $this->addFlash('danger', $message);
                    $statut = 0;
                }


                $data = true;

                //$message       = 'Opération effectuée avec succès';

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

        return $this->render('inscription/inscription/edit_etudiant.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'frais' => $fraisRepository->findBy(array('inscription' => $inscription->getId())),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_inscription_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_inscription_inscription_delete',
                    [
                        'id' => $inscription->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($inscription);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_inscription_inscription_index');

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

        return $this->render('inscription/inscription/delete.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }
}
