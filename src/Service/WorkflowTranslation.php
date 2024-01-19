<?php

namespace App\Service;

class WorkflowTranslation
{
    private static $maps = [
        'achat' => [
            'fournisseur' => 'Demande de cotation',
            'commande' => 'Bon de commande',
            'choix_fournisseur' => 'Demande cotations',
            'cote' => 'Cotés',
            'attribue' => 'Attribution',
            'service' => 'En attente R.H',
            'validation_achat' => 'En attente R.A',
            'validation' => 'En attente validation DIR.',
            'valide_rf' => 'En attente validation DIR.',
            'valide_dir' => 'En attente Validation R.F',
            'valide_achat' => 'En attente C.G'
        ],
        'commande' => [
            'valide' => 'Validé',
            'valide_cg' => 'Validé C.G',
            'attente_service' => 'En attente C.S',
            'service' => 'En attente D.T',
            'technique' => 'En attente C.G',
            'cree' => 'En attente de validation',
            'cloture' => 'Cloturé',
            'livre' => 'Livré',
        ]
    ];


    public static function label(string $module, string $state): string
    {
        return static::$maps[$module][$state] ?? $state;
    }
}