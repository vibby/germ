<?php

namespace GermBundle\Twig;

class PersonExtension extends \Twig_Extension
{
    private $coloredRoles = ['ROLE_ELDER', 'ROLE_DEACON', 'ROLE_DEACONESS'];

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colorizeRoles', array($this, 'colorizeRoles')),
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