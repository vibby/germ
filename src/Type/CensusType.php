<?php

namespace Germ\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Germ\Model\Germ\ChurchSchema\ChurchModel;
use PommProject\Foundation\Pomm;
use Symfony\Component\Form;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CensusType extends Form\AbstractType
{
    private $authorizationChecker;
    private $churchModel;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Pomm $pomm
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->churchModel = $pomm['germ']->getModel(ChurchModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', Type\BirthdayType::class, [
                'required' => false,
                'render_optional_text' => false,
            ])
            ->add('count', Type\IntegerType::class, array(
                'required'  => true,
                'render_optional_text' => false,
            ))
        ;
        if ($this->authorizationChecker->isGranted('ROLE_CHURCH_LIST')) {
            $builder->add('church_id', Type\ChoiceType::class, [
                'choices' => $this->churchModel->choiceId(),
                'required'  => true,
                'render_optional_text' => false,
                'label' => 'Church',
                'choice_translation_domain' => false,
            ]);
        }
    }
}
