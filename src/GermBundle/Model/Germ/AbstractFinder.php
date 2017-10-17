<?php

namespace GermBundle\Model\Germ;

use PommProject\Foundation\Where;

abstract class AbstractFinder
{
    abstract protected static function getModelClassName();

    public function findWhere(Where $where, $limitToUserChurch = true, $withActive = true, $withDeleted = false)
    {
        $this->alterWhere($where, $limitToUserChurch, $withActive, $withDeleted);

        return $this->model->findWhere($where);
    }

    public function countWhere(Where $where, $limitToUserChurch = true, $withActive = true, $withDeleted = false)
    {
        $this->alterWhere($where, $limitToUserChurch, $withActive, $withDeleted);

        return $this->model->countWhere($where);
    }

    public function alterWhere(Where $where, $limitToUserChurch = true, $withActive = true, $withDeleted = false)
    {
        return $where;
    }

    public function paginateFindWhere(Where $where, $item_per_page, $page)
    {
        $this->model->paginateFindWhere(
            $this->alterWhere($where),
            $item_per_page,
            $page
        );
    }
}
