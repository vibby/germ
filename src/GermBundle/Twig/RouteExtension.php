<?php

namespace GermBundle\Twig;

use Symfony\Component\HttpFoundation\Request;

class RouteExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cleanRequestParams', array($this, 'cleanRequestParams')),
        );
    }

    public function cleanRequestParams(Request $request, array $parameters)
    {
        $requestParams = array_filter(
            $request->attributes->all(),
            function ($key) {
                return (substr($key, 0, 1) !== '_');
            },
            ARRAY_FILTER_USE_KEY
        );

        return array_merge($requestParams, $parameters);
    }
}
