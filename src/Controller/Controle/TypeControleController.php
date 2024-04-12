<?php

namespace App\Controller\Controle;

use App\Entity\TypeControle;
use App\Form\TypeControleType;
use App\Repository\TypeControleRepository;
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

#[Route('/admin/controle/type/controle')]
class TypeControleController extends AbstractController
{

    function Rangeleve($case, $tab, $Nbr)
    {
        $rang = 1;
        /* for ($i = 1; $i < $Nbr; $i++) {
            if ($tab[$i] > $tab[$case]) {
                $rang = $rang + 1;
            }
        } */

        foreach ($tab as $key => $value) {
            if ($value > $tab[$case]) {
                $rang = $rang + 1;
            }
        }
        return $rang;
    }

    #[Route('/liste/type/controle', name: 'get_type_controle', methods: ['GET'])]
    public function getmatiere(Request $request, TypeControleRepository $typeControleRepository)
    {
        $response = new Response();
        $tabTypeContrrole = array();

        $typeControles = $typeControleRepository->findAll();

        $i = 0;

        foreach ($typeControles as $e) {
            // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
            $tabTypeContrrole[$i]['id'] = $e->getId();
            $tabTypeContrrole[$i]['libelle'] = $e->getCode();

            $i++;
        }

        $dataService = json_encode($tabTypeContrrole); // formater le résultat de la requête en json

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($dataService);

        return $response;
    }

    #[Route('/', name: 'app_controle_type_controle_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('coef', TextColumn::class, ['label' => 'Coefficent'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => TypeControle::class,
            ])
            ->setName('dt_app_controle_type_controle');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, TypeControle $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_controle_type_controle_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_type_controle_delete', ['id' => $value]),
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


        return $this->render('controle/type_controle/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_controle_type_controle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $typeControle = new TypeControle();
        $form = $this->createForm(TypeControleType::class, $typeControle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_type_controle_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_type_controle_index');




            if ($form->isValid()) {

                $entityManager->persist($typeControle);
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

        return $this->render('controle/type_controle/new.html.twig', [
            'type_controle' => $typeControle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_type_controle_show', methods: ['GET'])]
    public function show(TypeControle $typeControle): Response
    {
        return $this->render('controle/type_controle/show.html.twig', [
            'type_controle' => $typeControle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_type_controle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeControle $typeControle, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(TypeControleType::class, $typeControle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_type_controle_edit', [
                'id' =>  $typeControle->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_type_controle_index');




            if ($form->isValid()) {

                $entityManager->persist($typeControle);
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

        return $this->render('controle/type_controle/edit.html.twig', [
            'type_controle' => $typeControle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_type_controle_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, TypeControle $typeControle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_type_controle_delete',
                    [
                        'id' => $typeControle->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($typeControle);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_controle_type_controle_index');

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

        return $this->render('controle/type_controle/delete.html.twig', [
            'type_controle' => $typeControle,
            'form' => $form,
        ]);
    }
}
