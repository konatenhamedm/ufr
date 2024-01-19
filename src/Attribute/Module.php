<?php

namespace App\Attribute;
use Attribute;



#[Attribute(Attribute::TARGET_CLASS)]
class Module
{
    public function __construct(
        private string $name, 
        private ?string $title = '', 
        private ?string $controller = '', 
        private ?array $roles = [],
        private ?array $methods = [],
        private ?array $excludes = [],
        private ?array $children = []
    )
    {
        
    }
}