<?php

namespace App\Controller\Controle;

use App\Entity\Controle;
use App\Entity\Etudiant;
use App\Entity\GroupeType;
use App\Entity\MoyenneMatiere;
use App\Entity\Note;
use App\Entity\TypeControle;
use App\Entity\ValeurNote;
use App\Form\ControleType;
use App\Repository\ClasseRepository;
use App\Repository\ControleRepository;
use App\Repository\CoursRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;
use App\Repository\MatiereRepository;
use App\Repository\MatiereUeRepository;
use App\Repository\MoyenneMatiereRepository;
use App\Repository\NoteRepository;
use App\Repository\SemestreRepository;
use App\Repository\SessionRepository;
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
                            'edit' => [
                                'target' => '#exampleModalSizeSm2',
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

    function Rangeleve($case, $tab, $Nbr)
    {
        $rang = 1;

        foreach ($tab as $key => $value) {
            if ($value > $tab[$case]) {
                $rang = $rang + 1;
            }
        }
        /*   for ($i = 1; $i < $Nbr; $i++) {
            if ($tab[$i] > $tab[$case]) {
                $rang = $rang + 1;
            }
        } */
        return $rang;
    }

    #[Route('/new/load/{semestre}/{classe}/{matiere}/{session}', name: 'app_controle_controle_new_load', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function new_load(
        Request $request,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager,
        TypeControleRepository $typeControleRepository,
        FormError $formError,
        EtudiantRepository $etudiantRepository,
        ControleRepository $controleRepository,
        CoursRepository $coursRepository,
        MatiereRepository $matiereRepository,
        ClasseRepository $classeRepository,
        SemestreRepository $semestreRepository,
        SessionRepository $sessionRepository,
        $semestre = null,
        $classe = null,
        $matiere = null,
        $session = null,
        MoyenneMatiereRepository $moyenneMatiereRepository,
        MatiereUeRepository $matiereUeRepository,
        NoteRepository $noteRepository,
    ): Response {

        $all = $request->query->all();


        $controleVefication = $controleRepository->findOneBy(['classe' => $classe, 'matiere' => $matiere, 'semestre' => $semestre, 'session' => $session]);
        //dd($controleVefication);


        if ($controleVefication) {

            $form = $this->createForm(ControleType::class, $controleVefication, [
                'method' => 'POST',
                'action' => $this->generateUrl('app_controle_controle_new_load', [
                    'semestre' => $semestre,
                    'classe' => $classe,
                    'matiere' => $matiere,
                    'session' => $session,
                ])
            ]);
        } else {

            $controle = new Controle();

            $groupe = new GroupeType();
            $groupe->setCoef('10');
            $groupe->setType($typeControleRepository->findOneBy(['code' => 'DS']));
            $groupe->setDateNote(new \DateTime());
            if (count($inscriptionRepository->findBy(['classe' => $classe])) > 0)
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
                'action' => $this->generateUrl('app_controle_controle_new_load', [
                    'semestre' => $semestre,
                    'classe' => $classe,
                    'matiere' => $matiere,
                    'session' => $session,
                ])
            ]);
        }

        $form->handleRequest($request);
        $dater = $classe;
        $data = null;
        $statutCode = Response::HTTP_OK;
        $showAlert = false;
        $fullRedirect = false;
        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_new_saisie_simple');


            $dataNotes = $form->get('notes')->getData();
            $groupeTypes = $form->get('groupeTypes')->getData();


            if ($form->isValid()) {
                //dd($this->Rangeleve(1, $tableau, 1));


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
                    $moyenneEtudiant = $somme / ($nbreTour == 1 ? $coef : $coef / $nbreTour);
                    $row->setMoyenneMatiere($moyenneEtudiant);

                    $moyenneMatiere = $moyenneMatiereRepository->findOneBy(['matiere' => $matiere, 'etudiant' => $row->getEtudiant()]);
                    if ($moyenneMatiere) {
                        $moyenneMatiere->setMoyenne($moyenneEtudiant);
                        $matiereUeValide = $matiereUeRepository->findOneBy(['matiere' => $matiere]);
                        $moyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non');
                        $entityManager->persist($moyenneMatiere);
                        $entityManager->flush();
                    } else {
                        $newMoyenneMatiere = new MoyenneMatiere();

                        $newMoyenneMatiere->setEtudiant($row->getEtudiant());
                        $newMoyenneMatiere->setMatiere($matiereRepository->find($matiere));
                        $newMoyenneMatiere->setMoyenne($moyenneEtudiant);

                        $matiereUeValide = $matiereUeRepository->findOneBy(['matiere' => $matiere]);

                        $newMoyenneMatiere->setValide($moyenneEtudiant  >= $matiereUeValide->getMoyenneValidation() ? 'Oui' : 'Non');
                        $newMoyenneMatiere->setSession($sessionRepository->find($session));
                        $entityManager->persist($newMoyenneMatiere);
                        $entityManager->flush();
                    }
                }


                if ($controleVefication) {

                    //dd($controleVefication);
                    $controleVefication->setCour($coursRepository->findOneBy(['classe' => $classe, 'matiere' => $matiere, 'anneeScolaire' => $semestreRepository->find($semestre)->getAnneeScolaire()->getId()]));
                    $controleVefication->setAnneeScolaire($semestreRepository->find($semestre)->getAnneeScolaire());
                    $controleVefication->setMatiere($matiereRepository->find($matiere));
                    $controleVefication->setClasse($classeRepository->find($classe));
                    $controleVefication->setSession($sessionRepository->find($session));
                    $controleVefication->setSemestre($semestreRepository->find($semestre));
                    $entityManager->persist($controleVefication);
                } else {
                    $controle->setCour($coursRepository->findOneBy(['classe' => $classe, 'matiere' => $matiere, 'anneeScolaire' => $semestreRepository->find($semestre)->getAnneeScolaire()->getId()]));
                    $controle->setAnneeScolaire($semestreRepository->find($semestre)->getAnneeScolaire());
                    $controle->setMatiere($matiereRepository->find($matiere));
                    $controle->setClasse($classeRepository->find($classe));
                    $controle->setSession($sessionRepository->find($session));
                    $controle->setSemestre($semestreRepository->find($semestre));
                    $entityManager->persist($controle);
                }
                $entityManager->flush();

                $tableau = [];

                foreach ($dataNotes as $allNotes) {

                    $tableau[$allNotes->getEtudiant()->getId()] = (int)$allNotes->getMoyenneMatiere();
                }

                foreach ($dataNotes as  $allNotes) {

                    foreach ($tableau as $key => $value) {

                        // dd($key, $tableau[$allNotes->getEtudiant()->getId()] ==);
                        if ($tableau[$allNotes->getEtudiant()->getId()] == $tableau[$key]) {
                            $rang = $this->Rangeleve($key, $tableau, count($tableau));
                            $note = $noteRepository->find($allNotes->getId());

                            if ($note) {
                                $note->setRang($rang);
                                $entityManager->persist($note);
                                $entityManager->flush();
                            }
                        }
                    }
                }

                // dd($tableau);

                if ($compteIfNoteSuperieurMax > 0) {
                    $showAlert = true;
                    $statut = 0;
                    $message       = sprintf('Désolé veillez bien vérifier les notes saisie il y a au moins une note supperieur a la moyenne max de la notes');
                } else {
                    $message       = 'Opération effectuée avec succès';
                    $statut = 1;
                    $fullRedirect = true;
                }

                $data = true;
                /*    $message       = 'Opération effectuée avec succès';
                $statut = 1; */
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
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'showAlert', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }

        return $this->render('controle/controle/new_load.html.twig', [
            'controle' => $controleVefication ?? $controle,
            'nombre' => $controleVefication ? $controleVefication->getGroupeTypes()->count() : (count($inscriptionRepository->findBy(['classe' => $classe])) > 0 ? 1 : 0),
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new', name: 'app_controle_controle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TypeControleRepository $typeControleRepository, FormError $formError, EtudiantRepository $etudiantRepository, ControleRepository $controleRepository): Response
    {

        $controle = new Controle();

        $groupe = new GroupeType();
        $groupe->setCoef('10');
        $groupe->setType($typeControleRepository->find(1));
        $groupe->setDateNote(new \DateTime());
        $controle->addGroupeType($groupe);

        foreach ($etudiantRepository->findAll() as $etudiant) {
            $note = new Note();
            $note->setEtudiant($etudiant);
            //$note->setNote('');
            $note->setMoyenneMatiere('0');

            $controle->addNote($note);
            $valeurNote = new ValeurNote();
            $valeurNote->setNote("12");
            $note->addValeurNote($valeurNote);

            /*  $valeurNote1 = new ValeurNote();
            $valeurNote1->setNote("14");
            $note->addValeurNote($valeurNote1); */
        }

        // dd($controleRepository->)
        $form = $this->createForm(ControleType::class, $controle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_controle_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $showAlert = false;
        $isAjax = $request->isXmlHttpRequest();

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

                        foreach ($groupeTypes as $key => $groupe) {
                            if ($key1 == $key) {

                                $note = (int)$groupe->getCoef() == 10 ? $value->getNote() * 2 * (int)$groupe->getType()->getCoef() : $value->getNote() * (int)$groupe->getType()->getCoef();

                                if ($value->getNote() > 10 && $groupe->getCoef() == 10) {
                                    $compteIfNoteSuperieurMax++;
                                }
                            }

                            $coef = $coef + (int)$groupe->getType()->getCoef();
                        }


                        $somme = $somme + $note;
                    }
                    // dd($somme / ($coef / 2), $note, $coef / 2);
                    $row->setMoyenneMatiere($somme / ($coef / 2));
                }


                $entityManager->persist($controle);
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
                /*    $message       = 'Opération effectuée avec succès';
                $statut = 1; */
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

        return $this->render('controle/controle/new.html.twig', [
            'controle' => $controle,
            'nombre' => 1,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/new/saisie/simple', name: 'app_controle_controle_new_saisie_simple', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function newSaisieSimple(
        Request $request,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager,
        TypeControleRepository $typeControleRepository,
        FormError $formError,
        EtudiantRepository $etudiantRepository,
        ControleRepository $controleRepository,
        CoursRepository $coursRepository,
        MatiereRepository $matiereRepository,
        ClasseRepository $classeRepository,
        SemestreRepository $semestreRepository,
        SessionRepository $sessionRepository
    ): Response {

        $semestre = $request->query->get('semestre');
        $classe = $request->query->get('classe');
        $session = $request->query->get('session');
        $matiere = $request->query->get('matiere');

        /// dd($semestre);
        $controleVefication = $controleRepository->findOneBy(['classe' => $classe, 'matiere' => $matiere, 'semestre' => $semestre, 'session' => $session]);

        if ($controleVefication) {
            $form = $this->createForm(ControleType::class, $controleVefication, [
                'method' => 'POST',
                'action' => $this->generateUrl('app_controle_controle_new_saisie_simple')
            ]);
        } else {
            $controle = new Controle();
            $groupe = new GroupeType();
            $groupe->setCoef('10');
            $groupe->setType($typeControleRepository->findOneBy(['code' => 'DS']));
            $groupe->setDateNote(new \DateTime());
            if (count($inscriptionRepository->findBy(['classe' => $classe])) > 0)
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
                'action' => $this->generateUrl('app_controle_controle_new_saisie_simple')
            ]);
        }
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $showAlert = false;
        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_controle_controle_new_saisie_simple');


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
                    $controleVefication->setMatiere($matiereRepository->find(1));
                    $controleVefication->setClasse($classeRepository->find(1));
                    $controleVefication->setSession($sessionRepository->find(1));
                    $controleVefication->setSemestre($semestreRepository->find(1));
                    $entityManager->persist($controleVefication);
                } else {
                    $controle->setMatiere($matiereRepository->find(1));
                    $controle->setClasse($classeRepository->find(1));
                    $controle->setSession($sessionRepository->find(1));
                    $controle->setSemestre($semestreRepository->find(1));
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



        return $this->render('controle/controle/index_new.html.twig', [
            'controle' => $controleVefication ?? $controle,
            'nombre' => $controleVefication ? $controleVefication->getGroupeTypes()->count() : (count($inscriptionRepository->findBy(['classe' => $classe])) > 0 ? 1 : 0),
            'form' => $form->createView(),
            'title' => 'Gestion des contrôles',
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
        //dd($controle->getGroupeTypes()->count());

        $form = $this->createForm(ControleType::class, $controle, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_controle_controle_edit', [
                'id' =>  $controle->getId()
            ])
        ]);

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

                        foreach ($groupeTypes as $key => $groupe) {
                            if ($key1 == $key) {

                                $note = (int)$groupe->getCoef() == 10 ? $value->getNote() * 2 * (int)$groupe->getType()->getCoef() : $value->getNote() * (int)$groupe->getType()->getCoef();

                                if ($value->getNote() > 10 && $groupe->getCoef() == 10) {
                                    $compteIfNoteSuperieurMax++;
                                }
                            }

                            $coef = $coef + (int)$groupe->getType()->getCoef();
                        }


                        $somme = $somme + $note;
                    }
                    // dd($somme / ($coef / 2), $note, $coef / 2);
                    $row->setMoyenneMatiere($somme / ($coef / 2));
                }
                $entityManager->persist($controle);
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

        return $this->render('controle/controle/edit.html.twig', [
            'controle' => $controle,
            'nombre' => $controle->getGroupeTypes()->count(),
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
