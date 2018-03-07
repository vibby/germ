<?php

namespace GermBundle\Filter\Church;

use GermBundle\Filter\Criteria\AbstractCriteriaTerms;

class CriteriaTerms extends AbstractCriteriaTerms
{
    protected static function getLabel()
    {
        return 'Name';
    }

    protected static function getFields()
    {
        return ['name'];
    }
}
