<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

class RouterInfo
{
    private const ROLE_NOT_ALLOWED = '_ROLE_NOT_ALLOWED';
    public function __construct(private RouterInterface $router, private ParameterBagInterface $parameterBag)
    {
    }

    public function getRoleByRouteName(string $routeName, string $moduleName, $child = null, $role = null): mixed
    {
        if ($role) {
            return $role || 'ROLE_ADMIN';
        }

        
        try {
            $collection = $this->router->getRouteCollection();
            $routeCollection  = $collection->get($routeName);
            $defaults  = $routeCollection->getDefaults();
            $controller = $defaults['_controller'] ?? '';
            if ($controller) {
                $controllers = $this->parameterBag->get("{$moduleName}.controllers");
                [$class, $method] = explode('::', $controller);
                $class = str_replace('Controller', '', class_basename($class));
                
                foreach ($controllers as $controllerMap) {
                    if ($controllerMap['name'] == $class) {
                        foreach ($controllerMap['methods'] as $controllerMethod) {
                            [$realMethod, $tmpRole] = explode('@', $controllerMethod);
                            if ($method == $realMethod) {
                                
                                $roleName = $tmpRole . '_' . strtoupper($moduleName) . '_' . strtoupper($class);
                                if ($child) {
                                    $roleName .= '_' . strtoupper($child);
                                }
                                return $roleName;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            return self::ROLE_NOT_ALLOWED;
        }

        return self::ROLE_NOT_ALLOWED;
       
    }
}
