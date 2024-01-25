<?php

namespace App\Controller\Comptabilite;

use App\Entity\Inscription;
use App\Entity\Preinscription;
use App\Form\InscriptionType;
use App\Repository\InfoPreinscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Mpdf\MpdfException;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\FileTrait;
use App\Service\Omines\Column\NumberFormatColumn;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/admin/comptabilite/inscription')]
class InscriptionController extends AbstractController
{
    use FileTrait;
    #[Route('/', name: 'app_comptabilite_inscription_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory, UserInterface $user): Response
    {
        $table = $dataTableFactory->create()
            ->add('codePreinscription', TextColumn::class, ['label' => 'Code Préinscription', 'field' => 'p.getCode'])
            // ->add('datePreinscription', DateTimeColumn::class, [
            //     'label' => 'Date inscription',
            //     'format' => 'd-m-Y'
            // ])
            ->add('nom', TextColumn::class, ['label' => 'Nom et prénoms', 'field' => 'etudiant.getNomComplet'])
            ->add('sigleNiveauFiliere', TextColumn::class, ['label' => 'Sigle niveau filière', 'field' => 'niveau.getFullLibelleSigle'])
            ->add('datePaiement', DateTimeColumn::class, ['label' => 'Date paiement', 'format' => 'd-m-Y H:i:s', 'field' => 'info.datePaiement'])
            ->add('montantPaiement', NumberFormatColumn::class, ['label' => 'Montant', 'field' => 'info.montant'])
            ->add('caissiere', TextColumn::class, ['label' => 'Caissiere', 'field' => 'etudiant.getNomComplet'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Preinscription::class,
                'query' => function (QueryBuilder $qb) use ($user) {
                    $qb->select(['p', 'niveau', 'filiere', 'etudiant', 'info'])
                        ->from(Preinscription::class, 'p')
                        ->join('p.niveau', 'niveau')
                        ->join('niveau.filiere', 'filiere')
                        ->join('p.etudiant', 'etudiant')
                        ->leftJoin('p.infoPreinscription', 'info')
                        ->andWhere('p.etat = :etat')
                        ->setParameter('etat', 'valide');
                    if ($this->isGranted('ROLE_ETUDIANT')) {
                        $qb->andWhere('p.etudiant = :etudiant')
                            ->setParameter('etudiant', $user->getPersonne());
                    }
                }
            ])
            ->setName('dt_app_comptabilite_inscription');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return false;
            }),
            'imprime' =>  new ActionRender(function () {
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
                            'edit' => [
                                'url' => $this->generateUrl('app_comptabilite_versement_index', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-cash',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['edit']
                            ],
                            'imprime' => [
                                'url' => $this->generateUrl('default_print_iframe', [
                                    'r' => 'app_comptabilite_print',
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
                            /*  'imprime' => [
                                'url' => $this->generateUrl('app_comptabilite_print', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% fa fa-print',
                                'attrs' => ['class' => 'btn-main', 'title' => 'Frais \'écolage'],
                                'render' => $renders['imprime']
                            ], */

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


        return $this->render('comptabilite/inscription/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @throws MpdfException
     */
    #[Route('/{id}/imprime', name: 'app_comptabilite_print', methods: ['GET'])]
    public function imprimer($id, Preinscription $preinscription, InfoPreinscriptionRepository $infoPreinscriptionRepository): Response
    {
        return $this->renderPdf("site/recu.html.twig", [
            'data' => $preinscription,
            //'data_info'=>$infoPreinscriptionRepository->findOneByPreinscription($preinscription)
        ], [
            'orientation' => 'P',
            'protected' => true,
            'showWaterkText' => true,
            'fontDir' => [
                $this->getParameter('font_dir') . '/arial',
                $this->getParameter('font_dir') . '/trebuchet',
            ]
        ], true, "");
        //return $this->renderForm("stock/sortie/imprime.html.twig");

    }


    #[Route('/new', name: 'app_comptabilite_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_inscription_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_inscription_index');




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

        return $this->render('comptabilite/inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_comptabilite_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('comptabilite/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comptabilite_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_comptabilite_inscription_edit', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_comptabilite_inscription_index');




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

        return $this->render('comptabilite/inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_comptabilite_inscription_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_comptabilite_inscription_delete',
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

            $redirect = $this->generateUrl('app_comptabilite_inscription_index');

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

        return $this->render('comptabilite/inscription/delete.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }
}
