<?php

namespace Germ\Legacy\Filter\Criteria;

use Symfony\Component\Form\Form;
use PommProject\ModelManager\Model\Projection;

abstract class AbstractCriteria
{
    protected $data;

    public function setData($data, $fromUrl = false)
    {
        $data = $fromUrl ? $this->unurlize($data) : $data;
        $this->data = $data;
    }

    abstract public function alterForm(Form &$form);

    abstract public function buildWhere();

    public function alterProjection(Projection &$projection)
    {
        return [];
    }

    public function alterOrderBy(&$orderBy)
    {
        return;
    }

    public function urlize($data)
    {
        return $data;
    }

    public function unurlize($data)
    {
        return $data;
    }

    abstract public static function getUrlPrefix();

    abstract public static function getFormName();
}
