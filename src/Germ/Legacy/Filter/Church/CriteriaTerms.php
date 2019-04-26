<?php

namespace Germ\Legacy\Filter\Church;

use Germ\Legacy\Filter\Criteria\AbstractCriteriaTerms;

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
