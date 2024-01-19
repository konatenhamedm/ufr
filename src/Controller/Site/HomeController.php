<?php

namespace App\Controller\Site;

use App\Controller\FileTrait;
use App\DTO\InscriptionDTO;
use App\Entity\Employe;
use App\Entity\Etudiant;
use App\Entity\Fonction;
use App\Entity\Inscription;
use App\Entity\NiveauEtudiant;
use App\Entity\Pays;
use App\Entity\Preinscription;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurGroupe;
use App\Form\CiviliteType;
use App\Form\EtudiantDocumentType;
use App\Form\EtudiantType;
use App\Form\RegisterType;
use App\Form\UtilisateurInscriptionSimpleType;
use App\Form\UtilisateurInscriptionType;
use App\Form\UtilisateurType;
use App\Repository\EmployeRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use App\Repository\FonctionRepository;
use App\Repository\GroupeRepository;
use App\Repository\NiveauEtudiantRepository;
use App\Repository\NiveauRepository;
use App\Repository\PaysRepository;
use App\Repository\PersonneRepository;
use App\Repository\PreinscriptionRepository;
use App\Repository\UtilisateurGroupeRepository;
use App\Repository\UtilisateurRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\FormError;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class HomeController extends AbstractController
{
    use FileTrait;

    protected const UPLOAD_PATH = 'media_etudiant';
    private $em;
    public function __construct(EntityManagerInterface $em)
    {

        $this->em = $em;
    }
    private function numero($code)
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Preinscription::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ($code . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route(path: '/', name: 'site_home')]
    public function index(Request $request, FiliereRepository $filiereRepository): Response
    {

        return $this->render('site/index.html.twig', ['filieres' => $filiereRepository->findAll()]);
    }


    #[Route(path: '/inscription', name: 'site_register')]
    public function inscription_login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        NiveauRepository $niveauRepository,
        PreinscriptionRepository $preinscriptionRepository,
        FormError $formError,
        UtilisateurGroupeRepository $utilisateurGroupeRepository,
        GroupeRepository $groupeRepository,
        FonctionRepository $fonctionRepository,
        UtilisateurRepository $utilisateurRepository,
        SendMailService $sendMailService
        //PreinscriptionRepository $preinscriptionRepository
    ): Response {
        $inscriptionDTO = new InscriptionDTO();
        $form = $this->createForm(RegisterType::class, $inscriptionDTO, [
            'method' => 'POST',
            //'type'=>'autre',
            'action' => $this->generateUrl('site_register'),
        ]);

        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();
        $redirect = $this->generateUrl($loginFormAuthenticator::DEFAULT_INFORMATION);
        $fullRedirect = false;
        if ($form->isSubmitted()) {
            $response = [];
            $fonction = $entityManager->getRepository(Fonction::class)->findOneByCode('ETD');
            $user = $utilisateurRepository->findOneByEmail($inscriptionDTO->getEmail());
            if ($form->isValid()) {

                if (!$user) {
                    $etudiant = new Etudiant();
                    $etudiant->setNom($inscriptionDTO->getNom());
                    $etudiant->setPrenom($inscriptionDTO->getPrenom());
                    $etudiant->setDateNaissance($inscriptionDTO->getDateNaissance());
                    $etudiant->setCivilite($inscriptionDTO->getCivilite());
                    $etudiant->setGenre($inscriptionDTO->getGenre());
                    $etudiant->setFonction($fonctionRepository->findOneBy(['code' => 'ETD']));
                    $etudiant->setLieuNaissance('');
                    $etudiant->setEmail($inscriptionDTO->getEmail());
                    $etudiant->setContact($inscriptionDTO->getContact());
                    $etudiant->setFonction($fonction);
                    $entityManager->persist($etudiant);

                    $utilisateur = new Utilisateur();
                    $utilisateur->setPassword($userPasswordHasher->hashPassword($utilisateur, $inscriptionDTO->getPlainPassword()));
                    $utilisateur->addRole('ROLE_ETUDIANT');
                    $utilisateur->setEmail($inscriptionDTO->getEmail());
                    $utilisateur->setPersonne($etudiant);
                    $utilisateur->setUsername($inscriptionDTO->getEmail());

                    $entityManager->persist($utilisateur);

                    $entityManager->flush();

                    $groupe = new UtilisateurGroupe();

                    $groupe->setUtilisateur($utilisateur);
                    $groupe->setGroupe($groupeRepository->findOneBy(['libelle' => 'Etudiants']));
                    $utilisateurGroupeRepository->add($groupe, true);

                    $userAuthenticator->authenticateUser(
                        $utilisateur,
                        $loginFormAuthenticator,
                        $request
                    );
                    $preinscription = new Preinscription();
                    $preinscription->setEtat('attente_paiement');
                    $preinscription->setEtudiant($etudiant);
                    $preinscription->setDatePreinscription(new \DateTime());
                    $preinscription->setNiveau($inscriptionDTO->getNiveau());
                    $preinscription->setUtilisateur($utilisateur);
                    $preinscription->setCode($this->numero($inscriptionDTO->getNiveau()->getFiliere()->getCode()));
                    $preinscriptionRepository->add($preinscription, true);


                    $info_user = [
                        'login' => $inscriptionDTO->getEmail(),
                        'password' => $inscriptionDTO->getPlainPassword()
                    ];

                    $context = compact('info_user');

                    // TO DO
                    $sendMailService->send(
                        'konatenhamed@ufrseg.enig-sarl.com',
                        $inscriptionDTO->getEmail(),
                        'Informations',
                        'content_mail',
                        $context
                    );

                    $statut = 1;
                    $message = 'Compte crée avec succès';
                    $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération vous pouvez consulter votre email');
                } else {

                    $existe = $preinscriptionRepository->findOneBy(['niveau' => $inscriptionDTO->getNiveau()]);
                    if ($existe) {
                        $statut = 1;
                        $message = 'cet étudiant  existe  déjà dans cette filière,veillez vous connecter';
                        $this->addFlash('danger', $message);
                    } else {

                        $preinscription = new Preinscription();
                        $preinscription->setEtat('attente_paiement');
                        $preinscription->setEtudiant($user->getPersonne());
                        $preinscription->setDatePreinscription(new \DateTime());
                        $preinscription->setNiveau($inscriptionDTO->getNiveau());
                        $preinscription->setUtilisateur($user);
                        $preinscription->setCode($this->numero($inscriptionDTO->getNiveau()->getFiliere()->getCode()));
                        $preinscriptionRepository->add($preinscription, true);
                        $userAuthenticator->authenticateUser(
                            $user,
                            $loginFormAuthenticator,
                            $request
                        );
                    }
                }

                $fullRedirect = true;
                /* $statut = 1;
                $message = 'Compte crée avec succès';
                $this->addFlash('success', 'Votre compte a été crée avec succès. Veuillez vous connecter pour continuer l\'opération');*/
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = 500;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
            }



            if ($isAjax) {
                return $this->json(compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }
        }


        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/site/information', name: 'site_information')]
    public function information(Request $request, UserInterface $user, PersonneRepository $personneRepository, FormError $formError, NiveauRepository $niveauRepository, UtilisateurRepository $utilisateurRepository): Response
    {
        $etudiant = $user->getPersonne();

        //dd($niveauRepository->findNiveauDisponible(21));

        $form = $this->createForm(EtudiantType::class, $etudiant, [
            'method' => 'POST',
            'type' => 'info',
            'action' => $this->generateUrl('site_information')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('site_information');




            if ($form->isValid()) {

                $personneRepository->add($etudiant, true);
                //$entityManager->flush();

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

        return $this->render('site/informations.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }

    #[Route(path: '/site/document', name: 'site_document')]
    public function document(Request $request, UserInterface $user, PersonneRepository $personneRepository, FormError $formError): Response
    {
        $etudiant = $user->getPersonne();

        //dd($etudiant);
        $validationGroups = ['Default', 'FileRequired', 'autre'];
        $form = $this->createForm(EtudiantDocumentType::class, $etudiant, [
            'method' => 'POST',
            'type' => 'document',
            'doc_options' => [
                'uploadDir' => $this->getUploadDir(self::UPLOAD_PATH, true),
                'attrs' => ['class' => 'filestyle'],
            ],
            'validation_groups' => $validationGroups,
            'action' => $this->generateUrl('site_document')
        ]);

        $data = null;
        $statutCode = Response::HTTP_OK;

        $isAjax = $request->isXmlHttpRequest();


        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('site_document');




            if ($form->isValid()) {

                $personneRepository->add($etudiant, true);
                //$entityManager->flush();

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

        return $this->render('site/document.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);

        //return $this->render('site/admin/pages/informations.html.twig');
    }
    #[Route(path: '/site/save/data', name: 'site_save_data')]
    public function savedata(Request $request, PaysRepository $paysRepository): Response
    {


        $tab = [
            ["val0" => "Afghanistan "],
            ["val0" => "Afrique du Sud "],
            ["val0" => "Åland (les Îles)"],
            ["val0" => "Albanie "],
            ["val0" => "Algérie "],
            ["val0" => "Allemagne "],
            ["val0" => "Andorre "],
            ["val0" => "Angola "],
            ["val0" => "Anguilla"],
            ["val0" => "Antarctique "],
            ["val0" => "Antigua-et-Barbuda"],
            ["val0" => "Arabie saoudite "],
            ["val0" => "Argentine "],
            ["val0" => "Arménie "],
            ["val0" => "Aruba"],
            ["val0" => "Australie "],
            ["val0" => "Autriche "],
            ["val0" => "Azerbaïdjan "],
            ["val0" => "Bahamas "],
            ["val0" => "Bahreïn"],
            ["val0" => "Bangladesh "],
            ["val0" => "Barbade "],
            ["val0" => "Bélarus "],
            ["val0" => "Belgique "],
            ["val0" => "Belize "],
            ["val0" => "Bénin "],
            ["val0" => "Bermudes "],
            ["val0" => "Bhoutan "],
            ["val0" => "Bolivie (État plurinational de)"],
            ["val0" => "Bonaire, Saint-Eustache et Saba"],
            ["val0" => "Bosnie-Herzégovine "],
            ["val0" => "Botswana "],
            ["val0" => "Bouvet (l'Île)"],
            ["val0" => "Brésil "],
            ["val0" => "Brunéi Darussalam "],
            ["val0" => "Bulgarie "],
            ["val0" => "Burkina Faso "],
            ["val0" => "Burundi "],
            ["val0" => "Cabo Verde"],
            ["val0" => "Caïmans (les Îles)"],
            ["val0" => "Cambodge "],
            ["val0" => "Cameroun "],
            ["val0" => "Canada "],
            ["val0" => "Chili "],
            ["val0" => "Chine "],
            ["val0" => "Christmas (l'Île)"],
            ["val0" => "Chypre"],
            ["val0" => "Cocos (les Îles) / Keeling (les Îles)"],
            ["val0" => "Colombie "],
            ["val0" => "Comores "],
            ["val0" => "Congo "],
            ["val0" => "Congo (la République démocratique du)"],
            ["val0" => "Cook (les Îles)"],
            ["val0" => "Corée (la République de)"],
            ["val0" => "Corée (la République populaire démocratique de)"],
            ["val0" => "Costa Rica "],
            ["val0" => "Côte d'Ivoire "],
            ["val0" => "Croatie "],
            ["val0" => "Cuba"],
            ["val0" => "Curaçao"],
            ["val0" => "Danemark "],
            ["val0" => "Djibouti"],
            ["val0" => "dominicaine (la République)"],
            ["val0" => "Dominique "],
            ["val0" => "Égypte "],
            ["val0" => "El Salvador"],
            ["val0" => "Émirats arabes unis "],
            ["val0" => "Équateur "],
            ["val0" => "Érythrée "],
            ["val0" => "Espagne "],
            ["val0" => "Estonie "],
            ["val0" => "Eswatini "],
            ["val0" => "États-Unis d'Amérique "],
            ["val0" => "Éthiopie "],
            ["val0" => "Falkland (les Îles) /Malouines (les Îles)"],
            ["val0" => "Féroé (les Îles)"],
            ["val0" => "Fidji "],
            ["val0" => "Finlande "],
            ["val0" => "France "],
            ["val0" => "Gabon "],
            ["val0" => "Gambie "],
            ["val0" => "Géorgie "],
            ["val0" => "Géorgie du Sud-et-les Îles Sandwich du Sud "],
            ["val0" => "Ghana "],
            ["val0" => "Gibraltar"],
            ["val0" => "Grèce "],
            ["val0" => "Grenade "],
            ["val0" => "Groenland "],
            ["val0" => "Guadeloupe "],
            ["val0" => "Guam"],
            ["val0" => "Guatemala "],
            ["val0" => "Guernesey"],
            ["val0" => "Guinée "],
            ["val0" => "Guinée équatoriale "],
            ["val0" => "Guinée-Bissau "],
            ["val0" => "Guyana "],
            ["val0" => "Guyane française (la)"],
            ["val0" => "Haïti"],
            ["val0" => "Heard-et-Îles MacDonald (l'Île)"],
            ["val0" => "Honduras "],
            ["val0" => "Hong Kong"],
            ["val0" => "Hongrie "],
            ["val0" => "Île de Man"],
            ["val0" => "Îles mineures éloignées des États-Unis "],
            ["val0" => "Inde "],
            ["val0" => "Indien (le Territoire britannique de l'océan)"],
            ["val0" => "Indonésie "],
            ["val0" => "Iran (République Islamique d')"],
            ["val0" => "Iraq "],
            ["val0" => "Irlande "],
            ["val0" => "Islande "],
            ["val0" => "Israël"],
            ["val0" => "Italie "],
            ["val0" => "Jamaïque "],
            ["val0" => "Japon "],
            ["val0" => "Jersey"],
            ["val0" => "Jordanie "],
            ["val0" => "Kazakhstan "],
            ["val0" => "Kenya "],
            ["val0" => "Kirghizistan "],
            ["val0" => "Kiribati"],
            ["val0" => "Koweït "],
            ["val0" => "Lao (la République démocratique populaire)"],
            ["val0" => "Lesotho "],
            ["val0" => "Lettonie "],
            ["val0" => "Liban "],
            ["val0" => "Libéria "],
            ["val0" => "Libye "],
            ["val0" => "Liechtenstein "],
            ["val0" => "Lituanie "],
            ["val0" => "Luxembourg "],
            ["val0" => "Macao"],
            ["val0" => "Macédoine du Nord "],
            ["val0" => "Madagascar"],
            ["val0" => "Malaisie "],
            ["val0" => "Malawi "],
            ["val0" => "Maldives "],
            ["val0" => "Mali "],
            ["val0" => "Malte"],
            ["val0" => "Mariannes du Nord (les Îles)"],
            ["val0" => "Maroc "],
            ["val0" => "Marshall (les Îles)"],
            ["val0" => "Martinique "],
            ["val0" => "Maurice"],
            ["val0" => "Mauritanie "],
            ["val0" => "Mayotte"],
            ["val0" => "Mexique "],
            ["val0" => "Micronésie (États fédérés de)"],
            ["val0" => "Moldavie (la République de)"],
            ["val0" => "Monaco"],
            ["val0" => "Mongolie "],
            ["val0" => "Monténégro "],
            ["val0" => "Montserrat"],
            ["val0" => "Mozambique "],
            ["val0" => "Myanmar "],
            ["val0" => "Namibie "],
            ["val0" => "Nauru"],
            ["val0" => "Népal "],
            ["val0" => "Nicaragua "],
            ["val0" => "Niger "],
            ["val0" => "Nigéria "],
            ["val0" => "Niue"],
            ["val0" => "Norfolk (l'Île)"],
            ["val0" => "Norvège "],
            ["val0" => "Nouvelle-Calédonie "],
            ["val0" => "Nouvelle-Zélande "],
            ["val0" => "Oman"],
            ["val0" => "Ouganda "],
            ["val0" => "Ouzbékistan "],
            ["val0" => "Pakistan "],
            ["val0" => "Palaos "],
            ["val0" => "Palestine, État de"],
            ["val0" => "Panama "],
            ["val0" => "Papouasie-Nouvelle-Guinée "],
            ["val0" => "Paraguay "],
            ["val0" => "Pays-Bas "],
            ["val0" => "Pérou "],
            ["val0" => "Philippines "],
            ["val0" => "Pitcairn"],
            ["val0" => "Pologne "],
            ["val0" => "Polynésie française "],
            ["val0" => "Porto Rico"],
            ["val0" => "Portugal "],
            ["val0" => "Qatar "],
            ["val0" => "République arabe syrienne "],
            ["val0" => "République centrAfriqueine "],
            ["val0" => "Réunion "],
            ["val0" => "Roumanie "],
            ["val0" => "Royaume-Uni de Grande-Bretagne et d'Irlande du Nord "],
            ["val0" => "Russie (la Fédération de)"],
            ["val0" => "Rwanda "],
            ["val0" => "Sahara occidental"],
            ["val0" => "Saint-Barthélemy"],
            ["val0" => "Sainte-Hélène, Ascension et Tristan da Cunha"],
            ["val0" => "Sainte-Lucie"],
            ["val0" => "Saint-Kitts-et-Nevis"],
            ["val0" => "Saint-Marin"],
            ["val0" => "Saint-Martin (partie française)"],
            ["val0" => "Saint-Martin (partie néerlandaise)"],
            ["val0" => "Saint-Pierre-et-Miquelon"],
            ["val0" => "Saint-Siège "],
            ["val0" => "Saint-Vincent-et-les Grenadines"],
            ["val0" => "Salomon (les Îles)"],
            ["val0" => "Samoa "],
            ["val0" => "Samoa américaines "],
            ["val0" => "Sao Tomé-et-Principe"],
            ["val0" => "Sénégal "],
            ["val0" => "Serbie "],
            ["val0" => "Seychelles "],
            ["val0" => "Sierra Leone "],
            ["val0" => "Singapour"],
            ["val0" => "Slovaquie "],
            ["val0" => "Slovénie "],
            ["val0" => "Somalie "],
            ["val0" => "Soudan "],
            ["val0" => "Soudan du Sud "],
            ["val0" => "Sri Lanka"],
            ["val0" => "Suède "],
            ["val0" => "Suisse "],
            ["val0" => "Suriname "],
            ["val0" => "Svalbard et l'Île Jan Mayen "],
            ["val0" => "Tadjikistan "],
            ["val0" => "Taïwan (Province de Chine)"],
            ["val0" => "Tanzanie (la République-Unie de)"],
            ["val0" => "Tchad "],
            ["val0" => "Tchéquie "],
            ["val0" => "Terres australes françaises "],
            ["val0" => "Thaïlande "],
            ["val0" => "Timor-Leste "],
            ["val0" => "Togo "],
            ["val0" => "Tokelau "],
            ["val0" => "Tonga "],
            ["val0" => "Trinité-et-Tobago "],
            ["val0" => "Tunisie "],
            ["val0" => "Turkménistan "],
            ["val0" => "Turks-et-Caïcos (les Îles)"],
            ["val0" => "Turquie "],
            ["val0" => "Tuvalu "],
            ["val0" => "Ukraine "],
            ["val0" => "Uruguay "],
            ["val0" => "Vanuatu "],
            ["val0" => "Venezuela (République bolivarienne du)"],
            ["val0" => "Vierges britanniques (les Îles)"],
            ["val0" => "Vierges des États-Unis (les Îles)"],
            ["val0" => "Viet Nam "],
            ["val0" => "Wallis-et-Futuna"],
            ["val0" => "Yémen "],
            ["val0" => "Zambie "],
            ["val0" => "Zimbabwe"],
        ];

        foreach ($tab as $item) {

            $pays = new Pays();
            $pays->setLibelle($item['val0']);
            $this->em->persist($pays);
            $this->em->flush();
        }

        return $this->json('success');
    }
}
