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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AccountFormType extends AbstractType
{
    private $class;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', CollectionType::class, [
                // each entry in the array will be an "email" field
                'entry_type'   => ChoiceType::class,
                // these options are passed to each "email" type
                'entry_options'  => array(
                    'choices'  => array(
                        'Secretary' => 'ROLE_SECRETARY',
                        'Helder' => 'ROLE_HELDER',
                        'Pastor' => 'ROLE_PASTOR',
                        'User' => 'ROLE_USER',
                        'Administrator' => 'ROLE_ADMIN',
                    ),
                    'label'  => false,
                ),
                'allow_add'     =>true,
                'allow_delete'  =>true,
            ])
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'csrf_token_id' => 'account',
            // BC for SF < 2.8
            'intention'  => 'registration',
            'data_class' => 'GermBundle\Entity\Account',
        ));
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'germ_account';
    }
}
