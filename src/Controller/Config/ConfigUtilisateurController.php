<?php


namespace App\Controller\Config;

use App\Attribute\RoleMethod;
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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Workflow\Registry;

#[Route('config/utilisateur')]
class ConfigUtilisateurController extends AbstractController
{
    protected $workflow;

    public function __construct(Registry $workflow)
    {

        $this->workflow = $workflow;
    }

    #[Route(path: '/', name: 'app_config_utilisateur_index', methods: ['GET', 'POST'])]
    #[RoleMethod(title: 'Gestion des Paramètres', as: 'index')]
    public function index(Request $request, Breadcrumb $breadcrumb): Response
    {
        $module = $request->query->get('module');
        $modules = [
            [
                'label' => 'Utulisateurs administrateurs',
                'icon' => 'bi bi-list',
                'module' => 'general',
                'href' => $this->generateUrl('app_utilisateur_utilisateur_index', ['role' => 'all'])
            ],
            [
                'label' => 'Utilisateurs étudiants',
                'icon' => 'bi bi-list',
                'module' => 'gestion',
                'href' => $this->generateUrl('app_utilisateur_utilisateur_index', ['role' => 'etudiant'])
            ]
        ];

        $breadcrumb->addItem([
            [
                'route' => 'app_default',
                'label' => 'Tableau de bord'
            ],
            [
                'label' => 'Utilisateurs'
            ]
        ]);


        if ($module) {
            $modules = array_filter($modules, fn ($_module) => $_module['module'] == $module);
        }

        return $this->render('config/utilisateur/index.html.twig', [
            'modules' => $modules,
            'breadcrumb' => $breadcrumb
        ]);
    }
}
