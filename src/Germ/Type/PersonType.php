<?php

namespace Germ\Type;

use Symfony\Component\Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Germ\Person\RoleManager;
use Symfony\Component\Form\Extension\Core\Type;
use Germ\Person\Membership;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use PommProject\Foundation\Pomm;
use Germ\Model\Germ\ChurchSchema\ChurchModel;

class PersonType extends Form\AbstractType
{
    private $roleManager;
    private $membership;
    private $authorizationChecker;

    public function __construct(
        RoleManager $roleManager,
        Membership $membership,
        AuthorizationCheckerInterface $authorizationChecker,
        Pomm $pomm
    ) {
        $this->roleManager = $roleManager;
        $this->membership = $membership;
        $this->authorizationChecker = $authorizationChecker;
        $this->churchModel = $pomm['germ']->getModel(ChurchModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', Type\TextType::class)
            ->add('lastname', Type\TextType::class)
            ->add('birthdate', Type\BirthdayType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ])
            ->add('baptism_date', Type\BirthdayType::class, [
                'required'  => false,
                'render_optional_text' => false,
            ]);
        if ($this->authorizationChecker->isGranted('ROLE_PERSON_EDIT')) {
            $builder->add('membership_date', Type\BirthdayType::class, [
                'required' => false,
                'render_optional_text' => false,
            ])
            ->add('membership_act', Type\ChoiceType::class, [
                'choices' => $this->membership->getActChoices(),
                'required' => true,
                'render_optional_text' => false,
            ]);
        }
        $builder->add('phone', Type\CollectionType::class, array(
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
        ));
        if ($this->authorizationChecker->isGranted('ROLE_PERSON_EDIT')) {
            $builder->add('roles', Type\CollectionType::class, [
                'entry_type'   => Type\ChoiceType::class,
                'entry_options'  => array(
                    'choices' => $this->roleManager->getAssignable(),
                    'label'   => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ]);
        }
        if ($this->authorizationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $builder->add('church_id', Type\ChoiceType::class, [
                'choices' => $this->churchModel->choiceId(),
                'required'  => true,
                'render_optional_text' => false,
                'label' => 'Church',
                'choice_translation_domain' => false,
            ]);
            $builder->add('church_ids', Type\CollectionType::class, [
                'label' => 'Churches',
                'entry_type'   => Type\ChoiceType::class,
                'entry_options'  => array(
                    'choices' => $this->churchModel->choiceId(),
                    'label'   => false,
                    'choice_translation_domain' => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
                'mapped' => false,
            ]);
        }

        return $builder;
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
