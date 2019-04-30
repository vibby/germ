<?php

namespace Germ\Model\Germ\Church;

use Germ\Model\Germ\AbstractSaver;
use Germ\Model\Germ\ChurchSchema\Church;
use Germ\Model\Germ\ChurchSchema\ChurchModel;

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
