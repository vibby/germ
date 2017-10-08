<?php

namespace GermBundle\Twig;

use GermBundle\Person\SearchTerms;

class PersonExtension extends \Twig_Extension
{
    private $coloredRoles = ['ROLE_ELDER', 'ROLE_DEACON', 'ROLE_DEACONESS'];
    private $searchTerms;

    public function __construct(SearchTerms $searchTerms)
    {
        $this->searchTerms = $searchTerms;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colorizeRoles', array($this, 'colorizeRoles')),
            new \Twig_SimpleFilter('colorizeRole', array($this, 'colorizeRole')),
            new \Twig_SimpleFilter('highlightPerson', array($this->searchTerms, 'highlight'), ['is_safe' => ['html']]),
        );
    }

    public function colorizeRoles(array $roles)
    {
        $color = 888;
        foreach ($this->coloredRoles as $coloredRole) {
            if (in_array($coloredRole, $roles)) {
                $color = $this->stringToColorCode($coloredRole);
            }
        }

        return '#'.$color;
    }

    public function colorizeRole($role)
    {
        $color = 888;
        if (in_array($role, $this->coloredRoles)) {
            $color = $this->stringToColorCode($role);
        }

        return '#'.$color;
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