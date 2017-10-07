<?php

namespace GermBundle\Twig;

use GermBundle\Person\Searcher;

class PersonExtension extends \Twig_Extension
{
    private $coloredRoles = ['ROLE_ELDER', 'ROLE_DEACON', 'ROLE_DEACONESS'];
    private $searcher;

    public function __construct(Searcher $searcher)
    {
        $this->searcher = $searcher;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colorizeRoles', array($this, 'colorizeRoles')),
            new \Twig_SimpleFilter('highlightPerson', array($this->searcher, 'highlight'), ['is_safe' => ['html']]),
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