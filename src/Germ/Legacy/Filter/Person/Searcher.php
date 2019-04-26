<?php

namespace Germ\Legacy\Filter\Person;

use Germ\Legacy\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_person_filter';
    }
}
