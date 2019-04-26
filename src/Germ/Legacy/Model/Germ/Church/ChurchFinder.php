<?php

namespace Germ\Legacy\Model\Germ\Church;

use Germ\Legacy\Filter\FilterFinder;
use Germ\Legacy\Model\Germ\AbstractFinder;
use Germ\Legacy\Model\Germ\ChurchSchema\ChurchModel;
use PommProject\Foundation\Pomm;

class ChurchFinder extends AbstractFinder
{
    use FilterFinder;

    protected $model;

    public function __construct(Pomm $pomm)
    {
        $this->model = $pomm['germ']->getModel(self::getModelClassName());
    }

    public function getDefaultOrderBy()
    {
        return ['name'];
    }

    protected static function getModelClassName()
    {
        return ChurchModel::class;
    }
}
