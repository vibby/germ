<?php

namespace Germ\Legacy\Filter\Census;

use Germ\Legacy\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_census_filter';
    }
}
