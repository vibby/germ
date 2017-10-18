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
use Symfony\Component\OptionsResolver\OptionsResolver;
use GermBundle\Person\RoleManager;
use Symfony\Component\Form\Extension\Core\Type;
use GermBundle\Person\Membership;

class PersonType extends Form\AbstractType
{
    private $roleManager;
    private $membership;

    public function __construct(RoleManager $roleManager, Membership $membership)
    {
        $this->roleManager = $roleManager;
        $this->membership = $membership;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        return $builder
            ->add('firstname', Type\TextType::class)
            ->add('lastname', Type\TextType::class)
            ->add('birthdate', Type\DateType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ])
            ->add('membership_date', Type\DateType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ])
            ->add('membership_act', Type\ChoiceType::class, [
                'choices' => $this->membership->getActChoices(),
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
