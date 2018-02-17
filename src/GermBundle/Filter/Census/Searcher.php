<?php

namespace GermBundle\Filter\Census;

use GermBundle\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_census_filter';
    }
}
