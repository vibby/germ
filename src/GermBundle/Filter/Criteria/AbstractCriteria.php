<?php

namespace GermBundle\Filter\Criteria;

use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
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

    abstract public function alterProjection(Projection &$projection);

    abstract public function alterOrderBy(&$orderBy);

    abstract public function unurlize($data);

    abstract public function urlize($data);

    abstract public static function getUrlPrefix();

    abstract public static function getFormName();
}
