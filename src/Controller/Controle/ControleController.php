<?php

namespace App\Controller\Controle;

use App\Entity\Controle;
use App\Entity\Etudiant;
use App\Entity\Note;
use App\Form\ControleType;
use App\Repository\ControleRepository;
use App\Repository\EtudiantRepository;
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

#[Route('/admin/controle/controle')]
class ControleController extends AbstractController
{
    #[Route('/', name: 'app_controle_controle_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()

            ->createAdapter(ORMAdapter::class, [
                'entity' => Controle::class,
            ])
            ->setName('dt_app_controle_controle');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Controle $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',


                        'actions' => [
                            'target' => '#exampleModalSizeSm2',
                            'edit' => [
                                'url' => $this->generateUrl('app_controle_controle_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_controle_delete', ['id' => $value]),
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


        return $this->render('controle/controle/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_controle_controle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, EtudiantRepository $etudiantRepository): Response
    {
        $controle = new Controle();

        foreach ($etudiantRepository->findAll() as $etudiant) {
            $note = new Note();
            $note->setEtudiant($etudiant);
            $note->setNote('');
            $note->setMoyenneMatiere('');

            $controle->addNote($note);
        }
        $form = $this->createForm(ControleType::class, $controle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_controle_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_index');




            if ($form->isValid()) {

                $entityManager->persist($controle);
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

        return $this->render('controle/controle/new.html.twig', [
            'controle' => $controle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_controle_show', methods: ['GET'])]
    public function show(Controle $controle): Response
    {
        return $this->render('controle/controle/show.html.twig', [
            'controle' => $controle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_controle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Controle $controle, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(ControleType::class, $controle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_controle_edit', [
                'id' =>  $controle->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_index');




            if ($form->isValid()) {

                $entityManager->persist($controle);
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

        return $this->render('controle/controle/edit.html.twig', [
            'controle' => $controle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_controle_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Controle $controle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_controle_delete',
                    [
                        'id' => $controle->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($controle);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_controle_controle_index');

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

        return $this->render('controle/controle/delete.html.twig', [
            'controle' => $controle,
            'form' => $form,
        ]);
    }
}
