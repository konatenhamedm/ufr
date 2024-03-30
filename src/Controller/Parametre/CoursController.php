<?php

namespace App\Controller\Parametre;

use App\Entity\Classe;
use App\Entity\Cours;
use App\Entity\Matiere;
use App\Entity\Niveau;
use App\Form\CoursType;
use App\Form\NiveauAddEnseignantWithClasseType;
use App\Repository\AnneeScolaireRepository;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/parametre/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_parametre_cours_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $niveau = $request->query->get('niveau');


        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_parametre_cours_index', compact('niveau'))
        ])->add('niveau', EntityType::class, [
            'class' => Niveau::class,
            'choice_label' => 'getFullLibelleSigle',
            'label' => 'Niveau',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ]);

        $table = $dataTableFactory->create()
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])
            ->add('libelle', TextColumn::class, ['label' => 'Libelle'])
            ->add('annee', TextColumn::class, ['label' => 'Année', 'field' => 'annee.libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Classe::class,

                'query' => function (QueryBuilder $qb) use ($niveau) {
                    $qb->select('u, niveau, annee')
                        ->from(Classe::class, 'u')
                        ->join('u.niveau', 'niveau')
                        ->join('u.anneeScolaire', 'annee');
                    if ($niveau) {
                        if ($niveau) {
                            $qb->andWhere('m.id = :niveau')
                                ->setParameter('niveau', $niveau);
                        }
                    }
                }

            ])
            ->setName('dt_app_parametre_cours_' . $niveau);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
            'personnalise' =>  new ActionRender(function () {
                return true;
            }),
        ];

        $gridId = $niveau;
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
                                'url' => $this->generateUrl('app_parametre_cours_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-person-plus',
                                'attrs' => ['class' => 'btn-warning'],
                                'render' => $renders['personnalise']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_cours_delete', ['id' => $value]),
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


        return $this->render('parametre/cours/index.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }


    #[Route('/new', name: 'app_parametre_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, CoursRepository $coursRepository, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $cour = new Classe();
        $form = $this->createForm(NiveauAddEnseignantWithClasseType::class, $cour, [
            'method' => 'POST',
            'type' => 'new',
            'action' => $this->generateUrl('app_parametre_cours_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_cours_index');

            $data = $form->get('cours')->getData();

            $classe = $form->get('classe')->getData();




            if ($form->isValid()) {

                foreach ($data as $key => $cours) {
                    $cours->setClasse($classe);
                    $cours->setAnneeScolaire($anneeScolaireRepository->findOneBy(['actif' => true]));

                    $coursRepository->add($cours, true);
                }



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

        return $this->render('parametre/cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('parametre/cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Classe $cour, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(NiveauAddEnseignantWithClasseType::class, $cour, [
            'method' => 'POST',
            'type' => $cour->getId(),
            'action' => $this->generateUrl('app_parametre_cours_edit', [
                'id' =>  $cour->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_cours_index');




            if ($form->isValid()) {

                $entityManager->persist($cour);
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

        return $this->render('parametre/cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_cours_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_cours_delete',
                    [
                        'id' => $cour->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($cour);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_cours_index');

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

        return $this->render('parametre/cours/delete.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }
}
