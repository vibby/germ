<?php

namespace Germ\Filter\Person;

use Germ\Filter\Criteria\AbstractCriteriaTerms;

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
