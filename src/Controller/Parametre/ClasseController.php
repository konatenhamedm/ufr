<?php

namespace App\Controller\Parametre;

use App\Entity\Classe;
use App\Form\ClasseType;
use App\Form\NiveauAddEnseignantType;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ClasseRepository;
use App\Repository\CoursRepository;
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

#[Route('/admin/parametre/classe')]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'app_parametre_classe_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])
            ->add('annee', TextColumn::class, ['label' => 'Année', 'field' => 'annee.libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Classe::class,
                'query' => function (QueryBuilder $qb) {
                    $qb->select('u, niveau, annee')
                        ->from(Classe::class, 'u')
                        ->join('u.niveau', 'niveau')
                        ->join('u.anneeScolaire', 'annee');
                }
            ])
            ->setName('dt_app_parametre_classe');

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'personnalise' =>  new ActionRender(function () {
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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Classe $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_classe_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'personnalise' => [
                                'url' => $this->generateUrl('app_parametre_niveau_enseignant_matiere_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-person-plus',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['personnalise']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_classe_delete', ['id' => $value]),
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


        return $this->render('parametre/classe/index.html.twig', [
            'datatable' => $table
        ]);
    }



    #[Route('/{id}/enseignant/matiere/edit/', name: 'app_parametre_niveau_enseignant_matiere_edit', methods: ['GET', 'POST'])]
    public function editPersonnalisation(Request $request, Classe $classe, EntityManagerInterface $entityManager, FormError $formError, CoursRepository $coursRepository, AnneeScolaireRepository $anneeScolaireRepository): Response
    {

        $form = $this->createForm(NiveauAddEnseignantType::class, $classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_niveau_enseignant_matiere_edit', [
                'id' =>  $classe->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');

            $data = $form->get('cours')->getData();



            if ($form->isValid()) {



                foreach ($data as $key => $cours) {
                    $cours->setAnneeScolaire($anneeScolaireRepository->findOneBy(['actif' => true]));

                    $coursRepository->add($cours, true);
                }
                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/enseignant_matiere.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new', name: 'app_parametre_classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $classe = new Classe();
        $form = $this->createForm(ClasseType::class, $classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_classe_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');




            if ($form->isValid()) {

                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/new.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_classe_show', methods: ['GET'])]
    public function show(Classe $classe): Response
    {
        return $this->render('parametre/classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $classe, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(ClasseType::class, $classe, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_classe_edit', [
                'id' =>  $classe->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_classe_index');




            if ($form->isValid()) {

                $entityManager->persist($classe);
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

        return $this->render('parametre/classe/edit.html.twig', [
            'classe' => $classe,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/delete', name: 'app_parametre_classe_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Classe $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_classe_delete',
                    [
                        'id' => $classe->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($classe);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_classe_index');

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

        return $this->render('parametre/classe/delete.html.twig', [
            'classe' => $classe,
            'form' => $form,
        ]);
    }
}
