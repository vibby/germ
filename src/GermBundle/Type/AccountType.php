<?php

namespace GermBundle\Type;

use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AccountType extends Form\AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {

        if (!$options['person'] || !$options['person']->getEmail()) {
            $builder->add('email', Type\EmailType::class, [
                'label' => 'form.email', 'translation_domain' => 'FOSUserBundle',
            ]);
        }
        $builder->add('plainPassword', Type\RepeatedType::class, [
            'type' => Type\PasswordType::class,
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ]);
        if ($options['person'] && $options['person']->getEmail()) {
            $label = $this->translator->trans(
                'Send an email with connection access to :email_adress:',
                [':email_adress:' => $options['person']->getEmail()]
            );
        } else {
            $label = $this->translator->trans('Send an email with connection access');
        }
        $builder->add('sendEmail', Type\CheckboxType::class, [
            'label' => $label,
            'widget_checkbox_label' => 'widget',
            'mapped' => false,
            'required' =>false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'person' => null
        ));
    }
}
