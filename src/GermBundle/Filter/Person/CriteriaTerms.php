<?php

namespace GermBundle\Filter\Person;

use GermBundle\Filter\Criteria\AbstractCriteriaTerms;

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
