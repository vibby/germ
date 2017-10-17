<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GermBundle\Type;

use Symfony\Component\Form;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use GermBundle\Person\RoleManager;
use Symfony\Component\Form\Extension\Core\Type;

class PersonType extends Form\AbstractType
{
    private $roleManager;

    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add('firstname', Type\TextType::class)
            ->add('lastname', Type\TextType::class)
            ->add('birthdate', Type\TextType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ])
            ->add('phone', Type\CollectionType::class, array(
                'entry_type'    => Type\TextType::class,
                'entry_options' => array(
                    'required'  => false,
                    'attr'      => array('class' => 'phone-number'),
                    'label'     => false,
                    'render_optional_text' => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ))
            ->add('address', Type\TextareaType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('latlong', Type\TextType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('email', Type\EmailType::class, array(
                'required' => false,
                'render_optional_text' => false,
            ))
            ->add('roles', Type\CollectionType::class, [
                'entry_type'   => Type\ChoiceType::class,
                'entry_options'  => array(
                    'choices' => $this->roleManager->getAssignable(),
                    'label'   => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(Form\FormView $view, Form\FormInterface $form, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
