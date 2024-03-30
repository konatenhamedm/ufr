<?php

namespace App\Controller\Parametre;

use App\Entity\Cours;
use App\Entity\Semestre;
use App\Form\SemestreType;
use App\Repository\SemestreRepository;
use App\Service\ActionRender;
use App\Service\FormError;
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

#[Route('/admin/parametre/semestre')]
class SemestreController extends AbstractController
{
    #[Route('/', name: 'app_parametre_semestre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('dateDebut', DateTimeColumn::class, ['label' => 'Date début', 'format' => 'd-m-Y'])
            ->add('dateFin', DateTimeColumn::class, ['label' => 'Date fin', 'format' => 'd-m-Y'])
            ->add('actif', TextColumn::class, ['label' => 'Etat', 'className' => ' w-50px', 'render' => function ($value, Semestre $context) {

                if ($context->isActif() == true) {

                    return   '<span class="badge bg-success">Oui</span>';
                } else {

                    return   '<span class="badge bg-danger">Non</span>';
                }
            }])
            ->add('bloque', TextColumn::class, ['label' => 'Bloque', 'className' => ' w-50px'])
            ->add('coef', TextColumn::class, ['label' => 'Coefficient', 'className' => ' w-50px text-center'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Semestre::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select(['c', 'a'])
                        ->from(Semestre::class, 'c')
                        ->innerJoin('c.anneeScolaire', 'a')

                        ->orderBy('c.id', 'DESC');
                }
            ])
            ->setName('dt_app_parametre_semestre');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Semestre $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_semestre_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_semestre_delete', ['id' => $value]),
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


        return $this->render('parametre/semestre/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_semestre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, SemestreRepository $semestreRepository): Response
    {
        $semestre = new Semestre();
        $form = $this->createForm(SemestreType::class, $semestre, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_semestre_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_semestre_index');
            $data = $semestreRepository->findAll();

            $actif = $form->get('actif')->getData();



            if ($form->isValid()) {
                if ($actif) {

                    foreach ($data as $key => $semestre) {
                        $semestre->setActif(false);
                        $entityManager->persist($semestre);
                        $entityManager->flush();
                    }
                }
                $entityManager->persist($semestre);
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

        return $this->render('parametre/semestre/new.html.twig', [
            'semestre' => $semestre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_semestre_show', methods: ['GET'])]
    public function show(Semestre $semestre): Response
    {
        return $this->render('parametre/semestre/show.html.twig', [
            'semestre' => $semestre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_semestre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Semestre $semestre, EntityManagerInterface $entityManager, FormError $formError, SemestreRepository $semestreRepository): Response
    {

        $form = $this->createForm(SemestreType::class, $semestre, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_semestre_edit', [
                'id' =>  $semestre->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_semestre_index');

            $data = $semestreRepository->findAll();

            $actif = $form->get('actif')->getData();


            if ($form->isValid()) {
                if ($actif) {

                    foreach ($data as $key => $semestre) {
                        $semestre->setActif(false);
                        $entityManager->persist($semestre);
                        $entityManager->flush();
                    }
                }
                $semestre->setActif(true);
                $entityManager->persist($semestre);
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

        return $this->render('parametre/semestre/edit.html.twig', [
            'semestre' => $semestre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_semestre_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Semestre $semestre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_semestre_delete',
                    [
                        'id' => $semestre->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($semestre);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_semestre_index');

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

        return $this->render('parametre/semestre/delete.html.twig', [
            'semestre' => $semestre,
            'form' => $form,
        ]);
    }
}
