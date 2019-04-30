<?php

namespace Germ\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringifyExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('stringify', [$this, 'stringify']),
        ];
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
