<?php

namespace Germ\Filter\Sms;

use Germ\Filter\AbstractSearcher;

class Searcher extends AbstractSearcher
{
    public static function getRouteName()
    {
        return 'germ_sms_filter';
    }
}
