<?php

namespace Germ\Filter\Census;

use Germ\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_census_filter';
    }
}
