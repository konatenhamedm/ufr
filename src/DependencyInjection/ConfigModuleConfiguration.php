<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigModuleConfiguration implements ConfigurationInterface
{
    use ModuleTreeBuilder;
    
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return $this->buildNode('config');
    }
}