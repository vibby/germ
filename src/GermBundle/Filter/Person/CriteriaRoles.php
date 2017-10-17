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

class CriteriaRoles extends AbstractCriteria
{
    const SEPARATOR = ',';

    private $roleHierarchy;
    private $roleManager;

    public function __construct(RoleHierarchy $roleHierarchy, RoleManager $roleManager)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->roleManager = $roleManager;
    }

    public static function getUrlPrefix()
    {
        return 'role';
    }

    public static function getFormName()
    {
        return 'role';
    }

    public function urlize($data)
    {
        return $data ? implode(self::SEPARATOR, $this->roleManager->getStrings($data)) : '';
    }

    public function unurlize($data)
    {
        return $this->roleManager->getRoles(explode(self::SEPARATOR, $data));
    }

    public function alterForm(Form &$form)
    {
        $form->add(self::getUrlPrefix(), ChoiceType::class, [
            'label' => 'Role',
            'choices' => $this->roleManager->getFilterChoices(),
            'expanded' => true,
            'multiple' => true,
            'data' => $this->data,
        ]);
    }

    public function buildWhere()
    {
        if (!$this->data) {
            return null;
        }

        $where = Where::create();
        $roles = array_map(function ($roleName) { return new Role($roleName); }, $this->data);
        foreach ($this->roleHierarchy->getReachableRoles($roles) as $role) {
            $where->orWhere(Where::createGroupCondition('array_to_string(roles, \'||\')', 'ILIKE', [sprintf('%%%s%%', strtoupper($role->getRole()))]));
        }

        return $where;
    }

    public function alterProjection(Projection &$projection)
    {
        return [];
    }

    public function alterOrderBy(&$orderBy)
    {
        return;
    }
}
