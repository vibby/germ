<?php

namespace GermBundle\Person;

use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;

abstract class AbstractSearchItem
{
    const NO_FILTER_STRING = 'all';

    protected $data;

    public function setData($data, $needUnserializing = false)
    {
    	$data = $needUnserializing ? $this->unserialize($data) : $data;
        $this->data = $data;
    }

    abstract public function alterForm(Form &$form);

    abstract public function buildWhere();

    abstract public function alterProjection(Projection &$projection);

    abstract public function alterOrderBy(&$orderBy);

    abstract protected function unserialize($data);

    abstract public function serialize($data);
}
