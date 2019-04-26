<?php

namespace Germ\Legacy\Filter\Person;

use Germ\Legacy\Filter\Criteria\AbstractCriteriaTerms;

class CriteriaTerms extends AbstractCriteriaTerms
{
    protected static function getLabel()
    {
        return 'Lastname Firstname';
    }

    protected static function getFields()
    {
        return ['lastname', 'firstname'];
    }
}
