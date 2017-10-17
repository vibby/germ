<?php

namespace GermBundle\Model\Germ\ChurchSchema;

use GermBundle\Filter\FilterFinder;
use GermBundle\Model\Germ\AbstractFinder;
use PommProject\Foundation\Pomm;

class ChurchFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;

    public function __construct(Pomm $pomm)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
    }

    protected static function getModelClassName()
    {
        return ChurchModel::class;
    }
}
