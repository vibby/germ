<?php

namespace Germ\Filter\Person;

use Germ\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_person_filter';
    }
}
