<?php

namespace GermBundle\Filter\Person;

use GermBundle\Filter\Criteria\AbstractCriteria;
use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;
use GermBundle\Person\RoleManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use PommProject\Foundation\Pomm;
use GermBundle\Model\Germ\ChurchSchema\ChurchModel;

class CriteriaStatus extends AbstractCriteria
{
    const SEPARATOR = ',';

    private $authorisationChecker;
    private $model;
    protected $data = [0];

    public function __construct(AuthorizationChecker $authorisationChecker, Pomm $pomm)
    {
        $this->authorisationChecker = $authorisationChecker;
        $this->model = $pomm['germ']->getModel(ChurchModel::class);
    }

    public static function getUrlPrefix()
    {
        return 'deleted';
    }

    public static function getFormName()
    {
        return 'deleted';
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
            'label' => 'Status',
            'choices' => ['Active' => 0, 'Removed' => 1],
            'expanded' => true,
            'multiple' => true,
            'data' => $this->data,
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

        $where = Where::create();
        if ($this->data == [1]) {
            $where->orWhere(Where::create('is_deleted = true'));
        }
        if ($this->data == [0]) {
            $where->orWhere(Where::create('is_deleted = false'));
        }

        return $where;
    }

    private function checkAccess()
    {
        return $this->authorisationChecker->isGranted('ROLE_PERSON_DELETED');
    }
}
