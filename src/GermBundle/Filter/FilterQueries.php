<?php

namespace GermBundle\Filter;

use GermBundle\Model\Germ\AbstractFinder;
use PommProject\Foundation\Where;

trait FilterQueries
{
    public function paginateFilterQuery(AbstractSearcher $searcher, AbstractFinder $finder, $item_per_page, $page = 1)
    {
        $where = Where::create();
        $projection = $this->createProjection();
        $projectionValues = [];
        $orderBy = $finder->getDefaultOrderBy();
        foreach ($searcher->getCriterias() as $item) {
            $where->andWhere($item->buildWhere());
            $projectionValues += $item->alterProjection($projection);
            $item->alterOrderBy($orderBy);
        }
        $count = $finder->countWhere($where);
        $where = $finder->alterWhere($where);

        list($sql, $projection) = $this->findForListWhereSql($where, $projection, 'order by '.implode(', ', $orderBy));
        return [
            $count,
            $this->paginateQuery(
                $sql,
                array_merge($projectionValues, $where->getValues()),
                $count,
                $item_per_page,
                $page
            )
        ];
    }
}
