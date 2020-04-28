<?php

namespace Germ\Type;

use Germ\Model\Germ\CommunicationSchema\SmsModel;
use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type;
use Germ\Person\Membership;
use Germ\Person\RoleManager;
use PommProject\Foundation\Pomm;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SmsType extends Form\AbstractType
{
    private $roleManager;
    private $membership;
    private $authorizationChecker;
    private $smsModel;

    public function __construct(
        RoleManager $roleManager,
        Membership $membership,
        AuthorizationCheckerInterface $authorizationChecker,
        Pomm $pomm
    ) {
        $this->roleManager = $roleManager;
        $this->membership = $membership;
        $this->authorizationChecker = $authorizationChecker;
        $this->smsModel = $pomm['germ']->getModel(SmsModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', Type\TextareaType::class);

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