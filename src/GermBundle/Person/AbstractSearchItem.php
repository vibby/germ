<?php

namespace GermBundle\Person;

use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;

abstract class AbstractSearchItem
{
    protected $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    abstract public function alterForm(Form &$form);

    abstract public function buildWhere();

    abstract public function alterProjection(Projection &$projection);

    abstract public function alterOrderBy(&$orderBy);
}
