<?php

namespace Germ\Legacy\Filter;

trait FilterFinder
{
    public function paginateFilterQuery(AbstractSearcher $searcher, $item_per_page, $page = 1)
    {
        return $this->model->paginateFilterQuery($searcher, $this, $item_per_page, $page);
    }
}
