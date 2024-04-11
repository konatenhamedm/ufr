<?php

namespace App\Controller\Controle;

use App\Entity\Classe;
use App\Entity\Controle;
use App\Entity\Cours;
use App\Entity\GroupeType;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Entity\ValeurNote;
use App\Form\ControleType;
use App\Form\Cours1Type;
use App\Form\CoursType;
use App\Repository\ClasseRepository;
use App\Repository\ControleRepository;
use App\Repository\CoursRepository;
use App\Repository\InscriptionRepository;
use App\Repository\MatiereRepository;
use App\Repository\TypeControleRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Monolog\Handler\PushoverHandler;
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

#[Route('/admin/controle/cours')]
class CoursController extends AbstractController
{
    #[Route('/', name: 'app_controle_cours_index', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $matiere = $request->query->get('matiere');
        $classe = $request->query->get('classe');
        ///   dd($matiere, $classe);

        $builder = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl('app_controle_cours_index', compact('classe', 'matiere'))
        ])->add('classe', EntityType::class, [
            'class' => Classe::class,
            'choice_label' => 'libelle',
            'label' => 'Classe',
            'placeholder' => '---',
            'required' => false,
            'attr' => ['class' => 'form-control-sm has-select2']
        ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'libelle',
                'label' => 'Matiere',
                'placeholder' => '---',
                'required' => false,
                'attr' => ['class' => 'form-control-sm has-select2 matiere']
            ]);
        $table = $dataTableFactory->create()
            ->add('classe', TextColumn::class, ['label' => 'Classe', 'field' => 'c.libelle'])
            ->add('matiere', TextColumn::class, ['label' => 'matiere', 'field' => 'm.libelle'])
            ->add('annee', TextColumn::class, ['label' => 'Année scolaire', 'field' => 'a.libelle'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Cours::class,
                'query' => function (QueryBuilder $qb) use ($matiere, $classe) {
                    $qb->select(['co', 'c', 'm', 'a'])
                        ->from(Cours::class, 'co')
                        ->innerJoin('co.classe', 'c')
                        ->join('co.matiere', 'm')
                        ->join('co.anneeScolaire', 'a')
                        ->orderBy('co.id', 'DESC');


                    if ($matiere || $classe) {
                        if ($matiere) {
                            $qb->andWhere('m.id = :matiere')
                                ->setParameter('matiere', $matiere);
                        }
                        if ($classe) {
                            $qb->andWhere('c.id = :classe')
                                ->setParameter('classe', $classe);
                        }
                    }

                    /*  if ($this->isGranted('ROLE_DIRECTEUR')) {
                        $qb->andWhere('res.id = :id')
                            ->setParameter('id', $user->getPersonne()->getId());
                    } */
                }
            ])
            ->setName('dt_app_controle_cours_'  . $classe . '_' . $matiere);

        $renders = [
            'edit' =>  new ActionRender(function () {
                return true;
            }),
            'delete' => new ActionRender(function () {
                return false;
            }),
        ];

        $gridId =   $classe . '_' . $matiere;
        $hasActions = false;

        foreach ($renders as $_ => $cb) {
            if ($cb->execute()) {
                $hasActions = true;
                break;
            }
        }

        if ($hasActions) {
            $table->add('id', TextColumn::class, [
                'label' => 'Actions', 'orderable' => false, 'globalSearchable' => false, 'className' => 'grid_row_actions', 'render' => function ($value, Cours $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-sm btn-clean btn-icon mr-2 ',
                        'target' => '#modal-lg',

                        'actions' => [
                            'edit' => [
                                //'target' => '#exampleModalSizeSm2',
                                //'url' => $this->generateUrl('app_controle_controle_cours_edit', ['classe' => $context->getClasse()->getId(), 'matiere' => $context->getMatiere()->getId()]),
                                'url' => $this->generateUrl('app_controle_cours_edit', ['id' => $value]),
                                'ajax' => true,
                                'stacked' => false,
                                'icon' => '%icon% bi bi-pen',
                                'attrs' => ['class' => 'btn-main'],
                                'render' => $renders['edit']
                            ],
                            'delete' => [
                                'target' => '#modal-small',
                                'url' => $this->generateUrl('app_controle_cours_delete', ['id' => $value]),
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


        return $this->render('controle/cours/index.html.twig', [
            'datatable' => $table,
            'form' => $builder->getForm(),
            'grid_id' => $gridId
        ]);
    }


    #[Route('/new', name: 'app_controle_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FormError $formError): Response
    {
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_cours_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_cours_index');




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

        return $this->render('controle/cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_controle_cours_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('controle/cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_controle_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cour, EntityManagerInterface $entityManager, FormError $formError): Response
    {

        $form = $this->createForm(CoursType::class, $cour, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_cours_edit', [
                'id' =>  $cour->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_cours_index');




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

        return $this->render('controle/cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_controle_cours_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'app_controle_cours_delete',
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

            $redirect = $this->generateUrl('app_controle_cours_index');

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

        return $this->render('controle/cours/delete.html.twig', [
            'cour' => $cour,
            'form' => $form,
        ]);
    }


    #[Route('/liste/matiere', name: 'get_matiere', methods: ['GET'])]
    public function getmatiere(Request $request, CoursRepository $coursRepository)
    {
        $response = new Response();
        $tabMatieres = array();


        $id = $request->get('id');

        if ($id) {


            $matieres = $coursRepository->getMatiere($id);

            $i = 0;

            foreach ($matieres as $e) {
                // transformer la réponse de la requete en tableau qui remplira le select pour ensembles
                $tabMatieres[$i]['id'] = $e['id'];
                $tabMatieres[$i]['libelle'] = $e['libelle'];

                $i++;
            }

            $dataService = json_encode($tabMatieres); // formater le résultat de la requête en json

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($dataService);
        }
        return $response;
    }



    #[Route('/edit/new', name: 'app_controle_controle_cours_edit_new', methods: ['GET', 'POST'])]
    #[Route('/{classe}/{matiere}/edit', name: 'app_controle_controle_cours_edit', methods: ['GET', 'POST'])]
    public function editCOntrole(Request $request, TypeControleRepository $typeControleRepository, CoursRepository $coursRepository, MatiereRepository $matiereRepository, ClasseRepository $classeRepository, InscriptionRepository $inscriptionRepository, ControleRepository $controleRepository, EntityManagerInterface $entityManager, FormError $formError, $matiere, $classe): Response
    {

        $controleVefication = $controleRepository->findOneBy(['classe' => $classe, 'matiere' => $matiere]);

        if ($controleVefication) {
            $form = $this->createForm(ControleType::class, $controleVefication, [
                'method' => 'POST',
                'action' => $this->generateUrl('app_controle_controle_cours_edit', [
                    'classe' =>  $classe,
                    'matiere' => $matiere
                ])
            ]);
        } else {
            $controle = new Controle();
            $groupe = new GroupeType();
            $groupe->setCoef('10');
            $groupe->setType($typeControleRepository->findOneBy(['code' => 'DS']));
            $groupe->setDateNote(new \DateTime());
            $controle->addGroupeType($groupe);

            foreach ($inscriptionRepository->findBy(['classe' => $classe]) as $inscription) {
                $note = new Note();
                $note->setEtudiant($inscription->getEtudiant());
                //$note->setNote('');
                $note->setMoyenneMatiere('0');

                $controle->addNote($note);
                $valeurNote = new ValeurNote();
                $valeurNote->setNote("0");
                $note->addValeurNote($valeurNote);
            }

            $form = $this->createForm(ControleType::class, $controle, [
                'method' => 'POST',
                'action' => $this->generateUrl('app_controle_controle_cours_edit', [
                    'classe' =>  $classe,
                    'matiere' => $matiere
                ])
            ]);
        }

        $data = null;
        $statutCode = Response::HTTP_OK;
        $showAlert = false;
        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_index');

            $dataNotes = $form->get('notes')->getData();
            $groupeTypes = $form->get('groupeTypes')->getData();


            if ($form->isValid()) {
                $compteIfNoteSuperieurMax = 0;
                foreach ($dataNotes as $key => $row) {
                    $somme = 0;
                    $coef = 0;
                    foreach ($row->getValeurNotes() as $key1 => $value) {
                        $nbreTour = 0;
                        foreach ($groupeTypes as $key => $groupe) {
                            //$note = 0;
                            if ($key1 == $key) {

                                $note = (int)$groupe->getCoef() == 10 ? $value->getNote() * 2 * (int)$groupe->getType()->getCoef() : $value->getNote() * (int)$groupe->getType()->getCoef();

                                if ($value->getNote() > 10 && $groupe->getCoef() == 10) {
                                    $compteIfNoteSuperieurMax++;
                                }
                            }
                            if ($groupe->getType())
                                $coef = $coef + (int)$groupe->getType()->getCoef();
                            $nbreTour++;
                        }

                        $somme = $somme + $note;
                        // dd()
                    }
                    //dd($somme / ($coef / 2), $note, $coef);
                    $row->setMoyenneMatiere($somme / ($nbreTour == 1 ? $coef : $coef / 2));
                }
                if ($controleVefication) {
                    $controleVefication->setMatiere($matiereRepository->find($matiere));
                    $controleVefication->setClasse($classeRepository->find($classe));
                    $entityManager->persist($controleVefication);
                } else {
                    $controle->setMatiere($matiereRepository->find($matiere));
                    $controle->setClasse($classeRepository->find($classe));
                    $entityManager->persist($controle);
                }
                $entityManager->flush();

                if ($compteIfNoteSuperieurMax > 0) {
                    $showAlert = true;
                    $statut = 0;
                    $message       = sprintf('Désolé votre opération à échoué car le montant total  de votre échéancier est inferieur mon total à payer');
                } else {
                    $message       = 'Opération effectuée avec succès2';
                    $statut = 1;
                }
                $data = true;

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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        if ($controleVefication) {
            return $this->render('controle/controle/edit.html.twig', [
                'controle' => $controleVefication ?? $controle,
                'nombre' => $controleVefication ? $controleVefication->getGroupeTypes()->count() : $controle->getGroupeTypes()->count(),
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('controle/controle/new.html.twig', [
                'controle' => $controle,
                'nombre' => 1,
                'form' => $form->createView(),
            ]);
        }
    }
}
