<?php

namespace Germ\Legacy\Model\Germ\Church;

use Germ\Legacy\Model\Germ\AbstractSaver;
use Germ\Legacy\Model\Germ\ChurchSchema\Church;
use Germ\Legacy\Model\Germ\ChurchSchema\ChurchModel;

class ChurchSaver extends AbstractSaver
{
    protected static function getModelClassName()
    {
        return ChurchModel::class;
    }

    protected static function getEntityClassName()
    {
        return Church::class;
    }
}
