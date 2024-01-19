<?php

namespace App\Controller\Direction;

use App\Entity\Examen;
use App\Entity\Matiere;
use App\Entity\MatiereExamen;
use App\Form\ExamenType;
use App\Repository\ExamenRepository;
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

#[Route('/admin/direction/examen')]
class ExamenController extends AbstractController
{
    #[Route('/', name: 'app_direction_examen_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('code', TextColumn::class, ['label' => 'Code'])
        ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
        ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])
        ->add('dateExamen', DateTimeColumn::class, ['label' => 'Date Prévue', 'format' => 'd-m-Y'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Examen::class,
        ])
        ->setName('dt_app_direction_examen');

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
                'label' => 'Actions'
                , 'orderable' => false
                ,'globalSearchable' => false
                ,'className' => 'grid_row_actions'
                , 'render' => function ($value, Examen $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_direction_examen_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_direction_examen_delete', ['id' => $value]),
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


        return $this->render('direction/examen/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_direction_examen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $examen = new Examen();
        $matieres = $entityManager->getRepository(Matiere::class)->findAll();
        foreach ($matieres as $matiere) {
            $matiereExamen = new MatiereExamen();
            $matiereExamen->setMatiere($matiere);
            $examen->addMatiereExamen($matiereExamen);
        }
        $form = $this->createForm(ExamenType::class, $examen, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_direction_examen_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_direction_examen_index');




            if ($form->isValid()) {

                $entityManager->persist($examen);
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
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }


        }

        return $this->render('direction/examen/new.html.twig', [
            'examen' => $examen,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_direction_examen_show', methods: ['GET'])]
    public function show(Examen $examen): Response
    {
        return $this->render('direction/examen/show.html.twig', [
            'examen' => $examen,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_direction_examen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Examen $examen, EntityManagerInterface $entityManager, FormError $formError): Response
    {


        $matieres = $entityManager->getRepository(Matiere::class)->findAll();
        $oldMatieres = $examen->getMatiereExamens();
        if (!$oldMatieres->count()) {
            foreach ($matieres as $matiere) {
                $matiereExamen = $oldMatieres->filter(fn (MatiereExamen $matiereExamen) => $matiereExamen->getMatiere() == $matiere)->current();
                if (!$matiereExamen) {
                    $matiereExamen = new MatiereExamen();
                }
               
                $matiereExamen->setMatiere($matiere);
                $examen->addMatiereExamen($matiereExamen);
            }
    
        }
       

        $form = $this->createForm(ExamenType::class, $examen, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_direction_examen_edit', [
                    'id' =>  $examen->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

       if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_direction_examen_index');




            if ($form->isValid()) {

                $entityManager->persist($examen);
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
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }

        }

        return $this->render('direction/examen/edit.html.twig', [
            'examen' => $examen,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_direction_examen_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Examen $examen, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_direction_examen_delete'
                ,   [
                        'id' => $examen->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($examen);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_direction_examen_index');

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

        return $this->render('direction/examen/delete.html.twig', [
            'examen' => $examen,
            'form' => $form,
        ]);
    }
}
