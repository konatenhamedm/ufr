<?php

namespace App\Controller\Utilisateur;

use App\Attribute\Module;
use App\DTO\GroupeDTO;
use App\DTO\GroupeModuleDTO;
use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use App\Service\ActionRender;
use App\Service\FormError;
use App\Service\Role;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route('/admin/utilisateur/groupe')]
#[Module(name: 'config')]
class GroupeController extends AbstractController
{
    #[Route('/', name: 'app_utilisateur_groupe_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('libelle', TextColumn::class, ['label' => 'Libellé'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Groupe::class,
        ])
        ->setName('dt_app_utilisateur_groupe');

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
                , 'render' => function ($value, Groupe $context) use ($renders) {
                    $options = [
                        'default_class' => 'btn btn-xs btn-clean btn-icon mr-2 ',
                        'target' => '#exampleModalSizeLg2',
                            
                        'actions' => [
                            'edit' => [
                            'url' => $this->generateUrl('app_utilisateur_groupe_edit', ['id' => $value])
                            , 'ajax' => false
                            , 'icon' => '%icon% bi bi-pen'
                            , 'attrs' => ['class' => 'btn-default']
                            , 'render' => $renders['edit']
                        ],
                        'delete' => [
                            'target' => '#exampleModalSizeNormal',
                            'url' => $this->generateUrl('app_utilisateur_groupe_delete', ['id' => $value])
                            , 'ajax' => true
                            , 'icon' => '%icon% bi bi-trash'
                            , 'attrs' => ['class' => 'btn-main']
                            ,  'render' => $renders['delete']
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


        return $this->render('utilisateur/groupe/index.html.twig', [
            'datatable' => $table
        ]);
    }

    

    #[Route('/new', name: 'app_utilisateur_groupe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $groupeDTO = new GroupeDTO();

        [$maps, $titles] = $this->getModulesMap();
       
        
        
        $allRoles = [];
        $allowedRoles = [];
        $modules = [];
        $moduleLabels = [];
        $children = [];
        $roleLabels = [];
        $etats = [];
        $groupes = [];
        $moduleRoles = [];

       

        
        foreach ($maps as $module => $allProps) {
            $modules[] = $module;
          
            $moduleName = strtolower($module);
            $moduleLabels[$module] =  mb_strtoupper($this->getParameter($moduleName.'.name'));
            $moduleRoles[$module] = [];
            foreach ($allProps as $controller => $props) {
                $groupeModule = new GroupeModuleDTO();
              
                $roles = [];

                foreach ($props['roles'] as $role => $label) {
                    $roles[] = $role;
                    $allRoles[] = $role;
                    $moduleRoles[$module][$controller][] = ['role' => $role, 'label' => $label];
                    //$roleLabels[$role] = $label;
                }

                
                $groupeModule->setListRoles(array_merge($roles));
                $groupeDTO->addModule($groupeModule);
            }
        }
        
       
       
        $form = $this->createForm(GroupeType::class, $groupeDTO, [
            'method' => 'POST',
            'roles' => array_unique($allRoles),
            'action' => $this->generateUrl('app_utilisateur_groupe_new')
        ]);
        $form->handleRequest($request);

        $data = null;
        $statutCode = Response::HTTP_OK;
        $fullRedirect = true;
        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {
            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_groupe_index');

           


            if ($form->isValid()) {
                $groupe = new Groupe();
                $groupe->setLibelle($groupeDTO->getLibelle());
                $groupe->setDescription($groupeDTO->getDescription());
                $roles = array_merge($groupeDTO->getRoles(), $request->request->all()['group_roles'] ?? []);
                $groupe->setRoles($roles);
                $em->persist($groupe);
                $em->flush();
               
                $data = null;
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $statutCode = Response::HTTP_BAD_REQUEST;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data', 'fullRedirect'), $statutCode);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect, Response::HTTP_OK);
                }
            }

            
        }

        return $this->render('utilisateur/groupe/new.html.twig', [
            'groupe' => $groupeDTO,
            'modules' => $modules,
            'groupes' => $groupes,
            'title' => 'Nouveau groupe',
            'etats' => $etats,
            'titles' => $titles,
            'role_labels' => [],
            'module_labels' => $moduleLabels,
            'module_roles' => $moduleRoles,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_utilisateur_groupe_show', methods: ['GET'])]
    public function show(Groupe $groupe): Response
    {
        return $this->render('utilisateur/groupe/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupe $groupe, EntityManagerInterface $em, FormError $formError): Response
    {
         [$maps, $titles] = $this->getModulesMap();
        

        $oldRoles = $groupe->getRoles();

      
        $groupeDTO = new GroupeDTO();
        $groupeDTO->setLibelle($groupe->getLibelle());
        $groupeDTO->setDescription($groupe->getDescription());


        $allRoles = [];
        $allowedRoles = [];
        $modules = [];
        $moduleLabels = [];
        $children = [];
        $roleLabels = [];
        $etats = [];
        $groupes = [];
        $moduleRoles = [];

       

        
        foreach ($maps as $module => $allProps) {
            $modules[] = $module;
          
            $moduleName = strtolower($module);
            $moduleLabels[$module] =  mb_strtoupper($this->getParameter($moduleName.'.name'));
            $moduleRoles[$module] = [];
            foreach ($allProps as $controller => $props) {
                $groupeModule = new GroupeModuleDTO();
              
                $roles = [];

                foreach ($props['roles'] as $role => $label) {
                    $roles[] = $role;
                    $allRoles[] = $role;
                    $moduleRoles[$module][$controller][] = ['role' => $role, 'label' => $label];
                    //$roleLabels[$role] = $label;
                }

                
                $groupeModule->setListRoles(array_merge($roles));
                $groupeDTO->addModule($groupeModule);
            }
        }
      
        
        
        $form = $this->createForm(GroupeType::class, $groupeDTO, [
            'method' => 'POST',
            'roles' => array_unique($allRoles),
            'old_roles' => $oldRoles,
            'action' => $this->generateUrl('app_utilisateur_groupe_edit', ['id' =>  $groupe->getId()])
        ]);

        $maxElements = $form->get('modules')->getData()->count();

        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        $data = null;
        $code = 200;
        $fullRedirect = false;
        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('app_utilisateur_groupe_index');

            if ($form->isValid()) {
                $groupe->setLibelle($groupeDTO->getName());
                $groupe->setDescription($groupeDTO->getDescription());
                $roles = array_merge($groupeDTO->getRoles(), $request->request->all()['group_roles'] ?? []);

              
                $groupe->setRoles($roles);
        
                $em->persist($groupe);

                $em->flush();
                
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);
                $redirect = null;
                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                $code = 500;
                //$redirect = null;
                if (!$isAjax) {
                    $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect', 'data'), $code);
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }


        

        return $this->render('utilisateur/groupe/edit.html.twig', [
            'groupe' => $groupeDTO,
            'modules' => $modules,
            'groupes' => $groupes,
            'title' => 'Modification',
            'etats' => $etats,
            'old_roles' => $oldRoles,
            'titles' => $titles,
            'role_labels' => [],
            'module_labels' => $moduleLabels,
            'module_roles' => $moduleRoles,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_utilisateur_groupe_delete', methods: ['DELETE', 'GET'])]
    public function delete(Request $request, Groupe $groupe, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'app_utilisateur_groupe_delete'
                ,   [
                        'id' => $groupe->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = true;
            $em->remove($groupe);
            $em->flush();

            $redirect = $this->generateUrl('app_utilisateur_groupe_index');

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

        return $this->render('utilisateur/groupe/delete.html.twig', [
            'groupe' => $groupe,
            'form' => $form,
        ]);
    }


    private function getName($alias, $name)
    {
        return $alias ?: $name;
    }


    private function getModulesMap()
    {
        $modules = $this->getModules();
       
        $maps = [];
        $titles = [];
        $baseControllerPath = "App\\Controller";
        $mapChildren = [];
       
        foreach ($modules as $module) {
           
            try {
                $controllers = $this->getParameter("{$module}.controllers");
               
            
               
                $module = strtoupper($module);
                $maps[$module] = [];
                
                foreach ($controllers as $controller) {
                   
                  
                    $name = $controller['name'];
                    $alias = snake_case($controller['as']);
                    $currentName = $this->getName($alias, $name);
                    $children = $controller['children'];
                    $namespace = studly_case($controller['namespace'] ?? $module);
                    $controllerClass = "{$baseControllerPath}\\{$namespace}\\{$name}Controller";
                    $title = $controller['title'] ?? $currentName;
                    $titles[$name] = $title;
                    $methods = Utils::getMethods($controllerClass, $module, $name);

                    if ($methods) {
                        $maps[$module][$name] = ['title' => $title, 'roles' => $methods];
                    }

                   
                   
                }
            } catch (Throwable $e) {
               
            }

            if (!$maps[$module]) {
                unset($maps[$module]);
            }
        }

      

        return [$maps, $titles];
    }


    
    /**
     * Retourne la liste des modules
     *
     * @return array
     */
    private function getModules(): array
    {
        $configDir = $this->getParameter('kernel.project_dir').'/config/modules';
      
        $finder = new Finder();
        $finder->files()->in($configDir);

        $modules = [];

        foreach ($finder as $file) {
            $modules[] = $file->getBasename('.yaml');
        }

        return $modules;
    }

}
