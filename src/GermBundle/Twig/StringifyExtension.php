<?php

namespace GermBundle\Twig;

class StringifyExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('stringify', array($this, 'stringify')),
        );
    }

    public function stringify($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('d/m/Y H:i:s');
        }
        if ($value instanceof \DateInterval) {
            return $value->format('%h:%I:%S');
        }

        return (string) $value;
    }

    public function getName()
    {
        return 'stringify_extension';
    }
}