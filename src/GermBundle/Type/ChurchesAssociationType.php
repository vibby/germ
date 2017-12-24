<?php

namespace GermBundle\Type;

use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type;
use GermBundle\Model\Germ\ChurchSchema\ChurchModel;
use PommProject\Foundation\Pomm;

class ChurchesAssociationType extends Form\AbstractType
{
    private $model;

    public function __construct(Pomm $pomm)
    {
        $this->model = $pomm['germ']->getModel(ChurchModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
    }
}
