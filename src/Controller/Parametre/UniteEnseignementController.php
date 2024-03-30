<?php

namespace App\Controller\Parametre;

use App\Entity\UniteEnseignement;
use App\Form\UniteEnseignementType;
use App\Repository\CoursRepository;
use App\Repository\MatiereUeRepository;
use App\Repository\UniteEnseignementRepository;
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

#[Route('/admin/parametre/unite/enseignement')]
class UniteEnseignementController extends AbstractController
{
    #[Route('/', name: 'app_parametre_unite_enseignement_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('niveau', TextColumn::class, ['label' => 'Niveaux', 'field' => 'n.libelle'])
            ->add('semestre', TextColumn::class, ['label' => 'Semestre', 'field' => 's.libelle'])
            ->add('codeUe', TextColumn::class, ['label' => 'Code Ue'])
            ->add('libelle', TextColumn::class, ['label' => 'libelle'])
            ->add('attribut', TextColumn::class, ['label' => 'Attribut Ue'])
            ->add('coef', TextColumn::class, ['label' => 'Coefficient'])


            ->createAdapter(ORMAdapter::class, [
                'entity' => UniteEnseignement::class,
                'query' => function (QueryBuilder $builder) {
                    $builder->resetDQLPart('join');
                    $builder
                        ->select('e,n,s')
                        ->from(UniteEnseignement::class, 'e')
                        ->join('e.niveau', 'n')
                        ->join('e.semestre', 's');
                },
            ])
            ->setName('dt_app_parametre_unite_enseignement');

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
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, UniteEnseignement $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',


                        'actions' => [
                            'edit' => [
                                'target' => '#exampleModalSizeSm2',
                                'url' => $this->generateUrl('app_parametre_unite_enseignement_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_parametre_unite_enseignement_delete', ['id' => $value]),
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


        return $this->render('parametre/unite_enseignement/index.html.twig', [
            'datatable' => $table
        ]);
    }


    #[Route('/new', name: 'app_parametre_unite_enseignement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, MatiereUeRepository $matiereUeRepository): Response
    {
        $uniteEnseignement = new UniteEnseignement();
        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_unite_enseignement_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_unite_enseignement_index');

            $data = $form->get('matiereUes')->getData();

            $somme = 0;

            if ($form->isValid()) {

                foreach ($data as $key => $matiereUe) {
                    $somme += $matiereUe->getNombreCredit();
                }
                $uniteEnseignement->setTotalCredit($somme);

                $entityManager->persist($uniteEnseignement);
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

        return $this->render('parametre/unite_enseignement/new.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/liste/matiere/niveau', name: 'get_service', methods: ['GET'])]
    public function getService(Request $request, CoursRepository $coursRepository)
    {
        $response = new Response();
        $tabEnsemblesService = array();

        $id = '';
        $id = $request->get('niveau');
        //dd( $id);
        if ($id) {


            $matieres = $coursRepository->findBy(['niveau' => $id]);

            //dd($ensembles);

            $i = 0;

            foreach ($matieres as $e) { // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabEnsemblesService[$i]['id'] = $e->getMatiere()->getId();
                $tabEnsemblesService[$i]['libelle'] = $e->getMatiere()->getLibelle();
                $i++;
            }

            $dataService = json_encode($tabEnsemblesService); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);



            // dd($response);

        }
        return $response;
    }

    #[Route('/{id}/show', name: 'app_parametre_unite_enseignement_show', methods: ['GET'])]
    public function show(UniteEnseignement $uniteEnseignement): Response
    {
        return $this->render('parametre/unite_enseignement/show.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parametre_unite_enseignement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UniteEnseignement $uniteEnseignement, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_parametre_unite_enseignement_edit', [
                'id' =>  $uniteEnseignement->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_parametre_unite_enseignement_index');




            if ($form->isValid()) {

                $entityManager->persist($uniteEnseignement);
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

        return $this->render('parametre/unite_enseignement/edit.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_parametre_unite_enseignement_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, UniteEnseignement $uniteEnseignement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_parametre_unite_enseignement_delete',
                    [
                        'id' => $uniteEnseignement->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $entityManager->remove($uniteEnseignement);
            $entityManager->flush();

            $redirect = $this->generateUrl('app_parametre_unite_enseignement_index');

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

        return $this->render('parametre/unite_enseignement/delete.html.twig', [
            'unite_enseignement' => $uniteEnseignement,
            'form' => $form,
        ]);
    }
}
