<?php

namespace GermBundle\Filter\Church;

use GermBundle\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_church_filter';
    }
}
