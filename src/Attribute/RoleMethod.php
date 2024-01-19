<?php

namespace App\Attribute;
use Attribute;



#[Attribute(Attribute::TARGET_METHOD)]
class RoleMethod
{
    public function __construct(
        private ?string $title, 
        private ?string $as = '', 
        private array $options = [])
    {
        
    }
}