<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ConfigModuleExtension extends Extension
{

    public function getAlias(): string
    {
        return 'config';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new ConfigModuleConfiguration();

        $config = $this->processConfiguration($configuration, $configs);
        
        foreach ($config as $k => $v){
            $container->setParameter("config.{$k}", $v);
        }
    }
}