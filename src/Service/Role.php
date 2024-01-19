<?php

namespace App\Service;

class Role
{

    const ROLE_TS = 'ROLE_COMPTA_SORTIECAISSE_INDEX';
    
    public static function getRoles(): array
    {
        return [
            'ROLE_NEW'=> 'Création',
            'ROLE_EDIT'=>'Modification',
            'ROLE_DELETE'=> 'Suppression',
            'ROLE_SHOW'=> 'Voir',
            'ROLE_INDEX' => 'Liste',
            'ROLE_MANAGE' => 'Gestion',
            'ROLE_VALIDATE' => 'Validation',
            'ROLE_CHIFFRAGE' => 'Chiffrage',
            'ROLE_CONFIG' => 'Paramètrage',
            'ROLE_DOCS' => 'Documentation'
        ];
    }

    public static function getModuleMaps(): array
    {
        return  [];
    }
    
    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }
}