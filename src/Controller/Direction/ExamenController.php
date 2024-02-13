<?php

namespace App\Controller\Direction;

use App\Entity\Examen;
use App\Entity\Filiere;
use App\Entity\Matiere;
use App\Entity\MatiereExamen;
use App\Form\ExamenType;
use App\Repository\ExamenRepository;
use App\Repository\PreinscriptionRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\SendMailService;
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

#[Route('/admin/direction/examen')]
class ExamenController extends AbstractController
{
    #[Route('/', name: 'app_direction_examen_index',   methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $filiere = $request->query->get('filiere');

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_direction_examen_index', compact('filiere'))
        ])->add('filiere', EntityType::class, [
            'class' => Filiere::class,
            'choice_label' => 'libelle',
            'label' => 'Filière',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ]);

        $table = $dataTableFactory->create()
            ->add('code', TextColumn::class, ['label' => 'Code'])
            ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
            ->add('niveau', TextColumn::class, ['label' => 'Niveau', 'field' => 'niveau.libelle'])
            ->add('dateExamen', DateTimeColumn::class, ['label' => 'Date Prévue', 'format' => 'd-m-Y'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Examen::class,
                'query' => function (QueryBuilder $qb) use ($filiere) {
                    $qb->select(['d', 'n', 'f'])
                        ->from(Examen::class, 'd')
                        ->innerJoin('d.niveau', 'n')
                        ->innerJoin('n.filiere', 'f')
                        ->orderBy('d.id', 'DESC');

                    if ($filiere) {
                        if ($filiere) {
                            $qb->andWhere('f.id = :filiere')
                                ->setParameter('filiere', $filiere);
                        }
                    }
                }
            ])
            ->setName('dt_app_direction_examen_' . $filiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delib' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return true;
            }),
        ];
        $gridId = $filiere;

        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Examen $context) use ($renders) {
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
                            'delib' => [
                                'url' => $this->generateUrl('app_direction_deliberation_historique', ['id' => $value]),
                                'ajax' => false,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-folder2-open',
                                'attrs' => ['class' => 'btn-primary'],
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
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }


    #[Route('/new', name: 'app_direction_examen_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError, SendMailService $sendMailService, PreinscriptionRepository $preinscriptionRepository): Response
    {
        $examen = new Examen();
        $matieres = $entityManager->getRepository(Matiere::class)->findAll();
        /*  foreach ($matieres as $matiere) {
            $matiereExamen = new MatiereExamen();
            $matiereExamen->setMatiere($matiere);
            $examen->addMatiereExamen($matiereExamen);
        } */
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
            /*    $data = $preinscriptionRepository->findBy(array('niveau' => $examen->getNiveau(), 'etat' => 'valide'));

            $matieres = $examen->getMatiereExamens();

            $date = $examen->getDateExamen();

            $context = compact('data', 'date', 'matieres');


            foreach ($data as $key => $preinscription) {

                $sendMailService->send(
                    //'konatefvaly@gmail.com',
                    'konatenhamed@ufrseg.enig-sarl.com',
                    $preinscription->getEtudiant()->getEmail(),
                    'Informations',
                    'template',
                    $context
                );
            } */
            // TO DO




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
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
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
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
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
                    'app_direction_examen_delete',
                    [
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
