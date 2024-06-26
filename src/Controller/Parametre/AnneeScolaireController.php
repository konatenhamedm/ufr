<?php

namespace App\Controller\Parametre;

use App\Entity\AnneeScolaire;
use App\Form\AnneeScolaireType;
use App\Repository\AnneeScolaireRepository;
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

#[Route('/admin/parametre/annee/scolaire')]
class AnneeScolaireController extends AbstractController
{
    #[Route('/', name: 'app_parametre_annee_scolaire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date début', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('actif', TextColumn::class, ['label' => 'Etat', 'className' => ' w-50px', 'render' => function ($value, AnneeScolaire $context) {

                if ($context->isActif() == true) {

                    return   '<span class="badge bg-success">Oui</span>';
                } else {

                    return   '<span class="badge bg-danger">Non</span>';
                }
            }])
            ->add('verrou', TextColumn::class, ['label' => 'Verrouillé', 'className' => ' w-50px', 'render' => function ($value, AnneeScolaire $context) {

                if ($context->isVerrou() == true) {

                    return   '<span class="badge bg-success">Verrouillé</span>';
                } else {

                    return   '<span class="badge bg-success">Non Verrouillé</span>';
                }
            }])
            ->createAdapter(ORMAdapter::class, [
                'entity' => AnneeScolaire::class,
            ])
            ->setName('dt_app_parametre_annee_scolaire');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, AnneeScolaire $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_annee_scolaire_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_annee_scolaire_delete', ['id' => $value]),
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


        return $this->render('parametre/annee_scolaire/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_annee_scolaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $anneeScolaire = new AnneeScolaire();
        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_annee_scolaire_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_annee_scolaire_index');

            $data = $anneeScolaireRepository->findAll();

            $actif = $form->get('actif')->getData();
            //$anneeScolaire->setActif($actif);

            /// dd($actif);

            if ($form->isValid()) {
                if ($actif) {

                    foreach ($data as $key => $annee) {
                        $annee->setActif(false);
                        $entityManager->persist($annee);
                        $entityManager->flush();
                    }
                }

                $entityManager->persist($anneeScolaire);
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

        return $this->render('parametre/annee_scolaire/new.html.twig', [
            'annee_scolaire' => $anneeScolaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_annee_scolaire_show', methods: ['GET'])]
    public function show(AnneeScolaire $anneeScolaire): Response
    {
        return $this->render('parametre/annee_scolaire/show.html.twig', [
            'annee_scolaire' => $anneeScolaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_annee_scolaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnneeScolaire $anneeScolaire, EntityManagerInterface $entityManager, FormError $formError, AnneeScolaireRepository $anneeScolaireRepository): Response
    {

        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_annee_scolaire_edit', [
                'id' =>  $anneeScolaire->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_annee_scolaire_index');
            $data = $anneeScolaireRepository->findAll();
            $actif = $form->get('actif')->getData();

            if ($form->isValid()) {

                if ($actif) {
                    foreach ($data as $key => $annee) {
                        $annee->setActif(false);
                        $entityManager->persist($annee);
                        $entityManager->flush();
                    }
                }

                $anneeScolaire->setActif(true);

                $entityManager->persist($anneeScolaire);
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

        return $this->render('parametre/annee_scolaire/edit.html.twig', [
            'annee_scolaire' => $anneeScolaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_annee_scolaire_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, AnneeScolaire $anneeScolaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_annee_scolaire_delete',
                    [
                        'id' => $anneeScolaire->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($anneeScolaire);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_annee_scolaire_index');

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

        return $this->render('parametre/annee_scolaire/delete.html.twig', [
            'annee_scolaire' => $anneeScolaire,
            'form' => $form,
        ]);
    }
}
