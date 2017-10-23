<?php

namespace GermBundle\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ChurchType extends Form\AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', Type\TextType::class, array(
                'required'  => false,
                'attr'      => array('class' => 'phone-number'),
                'label'     => false,
                'render_optional_text' => false,
            ))
            ->add('address', Type\TextareaType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('latlong', Type\TextType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
        ;
    }
}
