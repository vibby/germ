<?php

namespace GermBundle\Filter\Person;

use GermBundle\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_person_filter';
    }
}
