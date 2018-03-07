<?php

namespace GermBundle\Filter\Person;

use GermBundle\Filter\Criteria\AbstractCriteria;
use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use PommProject\Foundation\Pomm;
use GermBundle\Model\Germ\ChurchSchema\ChurchModel;

class CriteriaChurch extends AbstractCriteria
{
    const SEPARATOR = ',';

    private $authorisationChecker;
    private $model;

    public function __construct(AuthorizationChecker $authorisationChecker, Pomm $pomm)
    {
        $this->authorisationChecker = $authorisationChecker;
        /** @var ChurchModel model */
        $this->model = $pomm['germ']->getModel(ChurchModel::class);
    }

    public static function getUrlPrefix()
    {
        return 'church';
    }

    public static function getFormName()
    {
        return 'church';
    }

    public function urlize($data)
    {
        return $data ? implode(self::SEPARATOR, $data) : '';
    }

    public function unurlize($data)
    {
        return explode(self::SEPARATOR, $data);
    }

    public function alterForm(Form &$form)
    {
        if (!$this->checkAccess()) {
            return;
        }
        $form->add(self::getFormName(), ChoiceType::class, [
            'label' => 'Church',
            'choices' => $this->model->choiceSlug(),
            'choice_translation_domain' => false,
            'expanded' => false,
            'multiple' => true,
            'render_optional_text' => false,
            'data' => $this->data,
            'required' => false,
        ]);
    }

    public function buildWhere()
    {
        if (!$this->checkAccess()) {
            return null;
        }
        if (!$this->data) {
            return null;
        }

        $churchIds = $this->model->findIdsFromSlugs($this->data);

        $where = Where::create();
        foreach ($churchIds as $churchId) {
            $where->orWhere(Where::create('church_id = $*', [$churchId]));
        }

        return $where;
    }

    private function checkAccess()
    {
        return $this->authorisationChecker->isGranted('ROLE_CHURCH_READ');
    }
}
