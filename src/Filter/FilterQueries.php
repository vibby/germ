<?php

namespace Germ\Filter;

use Germ\Model\Germ\AbstractFinder;
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

    public function findForListWhereSql(Where $where, $projection = null, $suffix = null)
    {
        if (!$projection) {
            $projection = $this->createProjection();
        }

        return [
            $this->getFindWhereSql($where, $projection, $suffix),
            $projection
        ];
    }
}
