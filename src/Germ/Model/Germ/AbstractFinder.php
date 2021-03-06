<?php

namespace Germ\Model\Germ;

use PommProject\Foundation\Where;

abstract class AbstractFinder
{
    abstract protected static function getModelClassName();

    public function findWhere(Where $where, $withActive = true, $withDeleted = false)
    {
        $this->alterWhere($where, $withActive, $withDeleted);

        return $this->model->findWhere($where);
    }

    public function countWhere(Where $where, $withActive = true, $withDeleted = false)
    {
        $this->alterWhere($where, $withActive, $withDeleted);

        return $this->model->countWhere($where);
    }

    public function alterWhere(Where $where)
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

    public function findAll()
    {
        return $this->model->findWhere($this->alterWhere(new Where()));
    }

    abstract public function getDefaultOrderBy();

    public function findOneBySlug($slug)
    {
        return $this->findWhere(new Where('slug = $1', [':slug' => $slug]))->current();
    }

    public function findOneById($id)
    {
        return $this->findWhere(new Where('id = $1', [':id' => $id]))->current();
    }
}
