<?php

namespace GermBundle\Model\Germ\Church;

use GermBundle\Model\Germ\AbstractSaver;
use GermBundle\Model\Germ\ChurchSchema\Church;
use GermBundle\Model\Germ\ChurchSchema\ChurchModel;

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
