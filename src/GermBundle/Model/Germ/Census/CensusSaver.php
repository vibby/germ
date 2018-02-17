<?php

namespace GermBundle\Model\Germ\Census;

use GermBundle\Model\Germ\AbstractSaver;
use GermBundle\Model\Germ\ChurchSchema\Census;
use GermBundle\Model\Germ\ChurchSchema\CensusModel;

class CensusSaver extends AbstractSaver
{
    protected static function getModelClassName()
    {
        return CensusModel::class;
    }

    protected static function getEntityClassName()
    {
        return Census::class;
    }
}
