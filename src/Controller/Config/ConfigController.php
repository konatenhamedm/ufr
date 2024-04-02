<?php


namespace App\Controller\Config;

use App\Attribute\RoleMethod;
use App\Entity\Echeancier;
use App\Entity\InfoInscription;
use App\Entity\Inscription;
use App\Form\InscriptionPayementType;
use App\Repository\EcheancierRepository;
use App\Repository\FraisInscriptionRepository;
use App\Repository\InfoInscriptionRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NaturePaiementRepository;
use App\Service\Breadcrumb;
use App\Service\FormError;
use App\Service\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Workflow\Registry;

#[Route('config/preinscription/etude/dossier')]
class ConfigController extends AbstractController
{
    protected $workflow;

    public function __construct(Registry $workflow)
    {

        $this->workflow = $workflow;
    }

    #[Route(path: '/', name: 'app_config_preinscription_etude_dossier_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Dossiers en attente de transmission',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'all'])
            ],
            [
                'label' => 'Dossiers transmis pour traitement',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_niveau_etudiant_preinscription_index', ['etat' => 'attente_validation'])
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/preinscription/index_etude_dossier.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }
    #[Route(path: '/point/paiement', name: 'app_config_preinscription_point_paiement_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexPointPaiement(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'PAIEMENTS PREINSCRIPTIONS',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ],
            [
                'label' => 'PAIEMENTS SCOLARITE',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/preinscription/index_point_paiement.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }


    #[Route(path: '/point/paiement/cheque', name: 'app_config_preinscription_point_paiement_cheque_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexPointPaiementCheque(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'EN ATTENTE DE CONFIRMATION',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_infoinscription_info_inscription_point_cheque_index')
            ],
            [
                'label' => 'CONFIRME',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_infoinscription_info_inscription_point_cheque_confirme_index')
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/preinscription/index_point_paiement_cheque.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }

    #[Route(path: '/paiement/inscription/{id}', name: 'app_config_paiement_inscription_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexPaiementInscription(
        Request $request,
        Breadcrumb $breadcrumb,
        $id,
        Inscription $inscription,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository,
        FormError $formError,
        FraisInscriptionRepository $fraisRepository,
        EcheancierRepository $echeancierRepository,
        UserInterface $user,
        NaturePaiementRepository $naturePaiementRepository,
        InfoInscriptionRepository $infoInscriptionRepository,
        Service $service
    ): Response {
        /* $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Point paiements ',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_inscription_inscription_paiement_ok', ['id' => $id])
            ],
            [
                'label' => 'Paiements effectués ',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_infoinscription_info_inscription_info_paiement_index', ['id' => $id])
            ]
        ]; */

        $form = $this->createForm(InscriptionPayementType::class, $inscription, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_inscription_inscription_paiement_ok', [
                'id' =>  $inscription->getId()
            ])
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_config_echeanciers');
            //$ligne = $form->get('echeanciers')->getData();

            $workflow_data = $this->workflow->get($inscription, 'inscription');

            $echeanciers  = $echeancierRepository->findAllEcheance($inscription->getId());
            $date = $form->get('datePaiement')->getData();
            $mode =   $mode = $naturePaiementRepository->find($form->get('modePaiement')->getData()->getId());
            $montant = (int) $form->get('montant')->getData();
            $sommeTotal = (int) $infoInscriptionRepository->getMontantInfoInscription($inscription);

            $all_data = [

                'echeanciers' => $echeanciers,
                'date' => $date,
                'modePaiement' => $mode,
                'montant' => $montant,
                'numeroCheque' => $form->get('numeroCheque')->getData(),
                'banque' => $form->get('banque')->getData(),
                'contact' => $form->get('contact')->getData(),
                'dateCheque' => $form->get('dateCheque')->getData()
            ];


            if ($form->isValid()) {

                $paiement = new InfoInscription();
                $paiement->setUtilisateur($this->getUser());
                $paiement->setCode($inscription->getCode());
                $paiement->setDateValidation(new \DateTime());
                $paiement->setInscription($inscription);
                $paiement->setDatePaiement($all_data['date']);
                $paiement->setCaissiere($this->getUser());
                $paiement->setModePaiement($all_data['modePaiement']);
                $paiement->setMontant($all_data['montant']);
                // $paiement->setEchenacier($echeancier);
                if ($all_data['modePaiement']->getCode() == 'CHQ') {
                    $paiement->setNumeroCheque($all_data['numeroCheque']);
                    $paiement->setBanque($all_data['banque']);
                    $paiement->setTireur($all_data['tireur']);
                    $paiement->setContact($all_data['contact']);
                    $paiement->setDateCheque($all_data['dateCheque']);
                }
                if ($all_data['modePaiement']->isConfirmation()) {
                    $paiement->setEtat('attente_confirmation');
                } else {
                    $paiement->setEtat('payer');
                }

                $entityManager->persist($paiement);
                $entityManager->flush();

                $sommeTotal = (int) $infoInscriptionRepository->getMontantInfoInscription($inscription);

                $service->paiementInscriptionNew($inscription, $sommeTotal, $all_data);

                $message       = sprintf('Opération effectuée avec succès');
                if ($inscription->getMontant() == $inscription->getTotalPaye()) {
                    $statut = 1;
                } else {
                    $statut = 0;
                    $this->addFlash('success', $message);
                }

                $showAlert = true;
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
                return $this->json(compact('statut', 'message', 'redirect', 'data'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }



        return $this->render('config/preinscription/index_paiement_inscription.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
            'echeanciers' => $echeancierRepository->findBy(array('inscription' => $inscription->getId())),
            'paiements' => $infoInscriptionRepository->getDataPaiementEffectue($id),
        ]);
    }
    #[Route(path: '/config/echeanciers', name: 'app_config_echeanciers', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexConfigEcheancier(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Attente de validation',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'echeance_soumis'])
            ],
            [
                'label' => 'Valider',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_inscription_inscription_list_ls', ['etat' => 'valide'])
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/echeancier/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,

        ]);
    }
    #[Route(path: '/config/scolarite', name: 'app_config_scolarite', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexConfigSoclarite(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Dossiers non soldés',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_inscription_inscription_frais_index')
            ],
            [
                'label' => 'Point des paiements',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/scolarite/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,

        ]);
    }
    #[Route(path: '/config/point/paiement', name: 'app_config_points_paiement', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function indexConfigPointsPaiement(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Points inscriptions',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ],
            [
                'label' => 'Points Préinscriptions',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_comptabilite_inscription_index')
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Paramètres'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/scolarite/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb,

        ]);
    }
}
