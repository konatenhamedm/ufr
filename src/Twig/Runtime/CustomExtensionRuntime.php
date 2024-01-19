<?php

namespace App\Twig\Runtime;

use App\Service\RouterInfo;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;
use TypeError;

class CustomExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RouterInfo $routerInfo, private Security $security)
    {
        // Inject dependencies if needed
    }

    public function getMenuRole($routeName, $moduleName, $child = null, $role = null)
    {
        return $this->routerInfo->getRoleByRouteName($routeName, $moduleName, $child, $role);
    }


    /**
     * Undocumented function
     *
     * @param string $module
     * @param string $itemName
     * @return void
     */
    public function getRoleFromItemName($module, $itemName)
    {
        [$controller, $roleName] = explode('.', $itemName);
        return 'role_'.$roleName.'_'.$module.'_'.$controller;
    }


    public function isMenuLinkDisplayable(ItemInterface $item, $moduleName, $childName, $as, $role = null)
    {       
        /**
         * @var \App\Entity\Utilisateur $user
         */
        $user = $this->security->getUser();
        $namePrefix = $item->getParent()->getExtra('name_prefix', '');
        $name = str_replace($namePrefix, '', $item->getName());

        if ($role) {
            return $user->hasRoles([$role, 'ROLE_ADMIN']);
        }
       
        return $user->hasRole('ROLE_ADMIN') ||
            $item->getExtra('no_check') ||
            !$moduleName ||
            $item->getExtra('is_title') ||
            $item->getChildren() ||
            $user->hasAllRoleOnModule('MANAGE', $moduleName, $name, $childName, $as) ||
            !$item->getUri();

    }


    public function localizeNumber($value, $upCaseFirstLetter = false)
    {
        $fmt = numfmt_create('fr_FR', \NumberFormatter::SPELLOUT);
        $data =  $fmt->format($value);
        if ($upCaseFirstLetter === true) {
            return strtoupper($data[0]).substr($data, 1);
        }
        return $data;
    }


    /**
     * @param $value
     * @return mixed
    */
    public function isInstanceOf($value, $class)
    {
        return $value instanceof $class;
    }


    public function formatNumber($value, $decimal = 0, $sep = '.', $thousandSep = ' ', $default = null)
    {
        if ($value == 0 && $default) {
            return $default;
        }
        $value = $value ? strval($value) : '0';
        $decimalLength = $decimal;
        if (strpos($value, '.') && $decimal == 0) {
            [,$decimal] = explode('.', $value);
            if (substr_count($decimal, '0') != strlen($decimal)) {
                $decimalLength = strlen($decimal);
            }
        }
        try {
            $value = preg_replace('/\.00$/', '', number_format($value, $decimalLength, $sep, $thousandSep));
        } catch (TypeError $e) {
            $value = $default;
        }
        return $value;
    }

}
