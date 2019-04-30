<?php

namespace Germ\Twig;

use Germ\Filter\Person\CriteriaTerms;
use Germ\Person\RoleManager;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PersonExtension extends AbstractExtension
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
        return [
            new TwigFilter('colorizeRoles', [$this, 'colorizeRoles']),
            new TwigFilter('colorizeRole', [$this, 'colorizeRole']),
            new TwigFilter('nameRole', [$this->roleManager, 'string']),
            new TwigFilter('highlightPerson', [$this->searchTerms, 'highlight'], ['is_safe' => ['html']]),
        ];
    }

    public function colorizeRoles(array $roles)
    {
        $color = null;
        foreach ($this->roleManager->getColored() as $coloredRole) {
            $hierarchyRoles = array_map(
                function (Role $role) {
                    return $role->getRole();
                },
                $this->roleHierarchy->getReachableRoles([$coloredRole])
            );
            foreach ($roles as $role) {
                if (\in_array($role, $hierarchyRoles)) {
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
                function (Role $role) {
                    return $role->getRole();
                },
                $this->roleHierarchy->getReachableRoles([$coloredRole])
            );
            if (\in_array($role, $hierarchyRoles)) {
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
