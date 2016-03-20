<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GermBundle\Form\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AccountPasswordFormType extends AbstractType
{
    private $class;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.new_password'),
                'second_options' => array('label' => 'form.new_password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'csrf_token_id' => 'change_password',
            'data_class' => 'GermBundle\Entity\Account',
        ));
    }
}
