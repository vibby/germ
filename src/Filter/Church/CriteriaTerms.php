<?php

namespace Germ\Filter\Church;

use Germ\Filter\Criteria\AbstractCriteriaTerms;

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
