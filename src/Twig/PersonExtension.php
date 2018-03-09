<?php

namespace Germ\Twig;

use Germ\Filter\Person\CriteriaTerms;
use Symfony\Component\Security\Core\Role\Role;
use Germ\Person\RoleManager;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class PersonExtension extends \Twig_Extension
{
    private $searchTerms;
    private $roleHierarchy;
    private $roleManager;

    public function __construct(
        CriteriaTerms $searchTerms,
        RoleHierarchyInterface $roleHierarchy,
        RoleManager $roleManager
    ) {
        $this->searchTerms = $searchTerms;
        $this->roleHierarchy = $roleHierarchy;
        $this->roleManager = $roleManager;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colorizeRoles', array($this, 'colorizeRoles')),
            new \Twig_SimpleFilter('colorizeRole', array($this, 'colorizeRole')),
            new \Twig_SimpleFilter('nameRole', array($this->roleManager, 'string')),
            new \Twig_SimpleFilter('highlightPerson', array($this->searchTerms, 'highlight'), ['is_safe' => ['html']]),
        );
    }

    public function colorizeRoles(array $roles)
    {
        $color = null;
        foreach ($this->roleManager->getColored() as $coloredRole) {
            $hierarchyRoles = array_map(
                function(Role $role) { return $role->getRole(); },
                $this->roleHierarchy->getReachableRoles([$coloredRole])
            );
            foreach ($roles as $role) {
                if (in_array($role, $hierarchyRoles)) {
                    $color = $this->stringToColorCode($coloredRole->getRole());
                }
            }
        }

        return '#'.($color ? $color : '888');
    }

    public function colorizeRole($role)
    {
        $color = null;
        foreach ($this->roleManager->getColored() as $coloredRole) {
            $hierarchyRoles = array_map(
                function(Role $role) {return $role->getRole();},
                $this->roleHierarchy->getReachableRoles([$coloredRole])
            );
            if (in_array($role, $hierarchyRoles)) {
                $color = $this->stringToColorCode($coloredRole->getRole());
            }
        }

        return '#'.($color ? $color : '888');
    }

    private function stringToColorCode($str)
    {
        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);

        return $code;
    }

    public function getName()
    {
        return 'person_extension';
    }
}
