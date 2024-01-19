<?php

namespace App\Controller\Parametre;

use App\Entity\Employe;
use App\Entity\Frais;
use App\Entity\Niveau;
use App\Entity\TypeFrais;
use App\Form\NiveauType;
use App\Repository\NiveauRepository;
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

#[Route('/admin/parametre/niveau')]
class NiveauController extends AbstractController
{
    #[Route('/', name: 'app_parametre_niveau_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('code', TextColumn::class, ['label' => 'Code'])
        ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
        ->add('filiere', TextColumn::class, ['label' => 'Filière', 'field' => 'filiere.libelle'])
        ->add('nom', TextColumn::class, ['field' => 'emp.nom', 'visible' => false])
        ->add('prenom', TextColumn::class, ['field' => 'emp.prenom', 'visible' => false])
        ->add('responsable', TextColumn::class, ['label' => 'Responsable', 'render' => fn ($value, Niveau $niveau) => $niveau->getResponsable()->getNomComplet()])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Niveau::class,
            'query' => function (QueryBuilder $builder) {
                $builder->resetDQLPart('join');
                $builder
                    ->select('niveau,filiere,emp')
                    ->from(Niveau::class,'niveau')
                    ->join('niveau.filiere', 'filiere')
                    ->join('niveau.responsable', 'emp');
                ;
            },
        ])
        ->setName('dt_app_parametre_niveau');

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
                , 'render' => function ($value, Niveau $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                'url' => $this->generateUrl('app_parametre_niveau_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#modal-small',
                            'url' => $this->generateUrl('app_parametre_niveau_delete', ['id' => $value]),
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


        return $this->render('parametre/niveau/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_niveau_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $niveau = new Niveau();
        $typeFrais = $entityManager->getRepository(TypeFrais::class)->findAll();
        foreach ($typeFrais as $type) {
            $frais = new Frais();
            $frais->setTypeFrais($type);
            $niveau->addFrai($frais);
        }
        
        $form = $this->createForm(NiveauType::class, $niveau, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_niveau_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_niveau_index');




            if ($form->isValid()) {

                $entityManager->persist($niveau);
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

        return $this->render('parametre/niveau/new.html.twig', [
            'niveau' => $niveau,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_parametre_niveau_show', methods: ['GET'])]
    public function show(Niveau $niveau): Response
    {
        return $this->render('parametre/niveau/show.html.twig', [
            'niveau' => $niveau,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_niveau_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Niveau $niveau, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $typeFrais = $entityManager->getRepository(TypeFrais::class)->findAll();
        $oldFrais = $niveau->getFrais();
        foreach ($typeFrais as $type) {
            $frais = $oldFrais->filter(fn (Frais $frais) => $frais->getTypeFrais() == $type)->current();
            if (!$frais) {
                $frais = new Frais();
            }
           
            $frais->setTypeFrais($type);
            $niveau->addFrai($frais);
        }

        $form = $this->createForm(NiveauType::class, $niveau, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_niveau_edit', [
                'id' =>  $niveau->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

       if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_niveau_index');




            if ($form->isValid()) {

                $entityManager->persist($niveau);
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

        return $this->render('parametre/niveau/edit.html.twig', [
            'niveau' => $niveau,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_niveau_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Niveau $niveau, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_parametre_niveau_delete'
                ,   [
                        'id' => $niveau->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($niveau);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_niveau_index');

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

        return $this->render('parametre/niveau/delete.html.twig', [
            'niveau' => $niveau,
            'form' => $form,
        ]);
    }
}
