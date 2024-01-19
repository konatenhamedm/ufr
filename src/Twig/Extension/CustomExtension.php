<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\CustomExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CustomExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('format_number', [CustomExtensionRuntime::class, 'formatNumber']),
            new TwigFilter('localize_number', [CustomExtensionRuntime::class, 'localizeNumber']),
            new TwigFilter('flip', 'array_flip'),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_menu_role', [CustomExtensionRuntime::class, 'getMenuRole']),
            new TwigFunction('get_role_from_item_name', [CustomExtensionRuntime::class, 'getRoleFromItemName']),
            new TwigFunction('is_menu_link_displayable', [CustomExtensionRuntime::class, 'isMenuLinkDisplayable']),
            new TwigFunction('is_instance', [CustomExtensionRuntime::class, 'isInstanceOf']),
        ];
    }
}
