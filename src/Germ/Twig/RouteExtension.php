<?php

namespace Germ\Twig;

use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RouteExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('cleanRequestParams', [$this, 'cleanRequestParams']),
        ];
    }

    public function cleanRequestParams(Request $request, array $parameters)
    {
        $requestParams = array_filter(
            $request->attributes->all(),
            function ($key) {
                return '_' !== substr($key, 0, 1);
            },
            ARRAY_FILTER_USE_KEY
        );

        return array_merge($requestParams, $parameters);
    }
}
