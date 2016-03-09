<?php

namespace GermBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate')
            ->add('phone', CollectionType::class, array(
                'entry_type'    => TextType::class,
                'entry_options' => array(
                    'required'  => false,
                    'attr'      => array('class' => 'phone-number'),
                    'label'     => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ))
            ->add('address', TextareaType::class, array(
                'required' => false,
            ))
            ->add('family')
            ->add('email', EmailType::class, array(
                'required' => false,
            ))
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GermBundle\Entity\Person',
        ));
    }
}