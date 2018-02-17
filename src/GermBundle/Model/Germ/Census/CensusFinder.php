<?php

namespace GermBundle\Model\Germ\Census;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use GermBundle\Model\Germ\ChurchSchema\CensusModel;
use PommProject\Foundation\Pomm;

class CensusFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;

    public function __construct(Pomm $pomm)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
    }

    public function getDefaultOrderBy()
    {
        return ['date'];
    }

    protected static function getModelClassName()
    {
        return CensusModel::class;
    }
}
