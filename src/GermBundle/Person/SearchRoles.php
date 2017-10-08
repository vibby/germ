<?php

namespace GermBundle\Person;

use Symfony\Component\Form\Form;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Projection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

class SearchRoles extends AbstractSearchItem
{
    const NAME = 'roles';

    private $roleHierarchy;

    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function alterForm(Form &$form)
    {
        $form->add(self::NAME, ChoiceType::class, [
            'label' => 'Role',
            'choices' => [
                'ROLE_DIACONATE' => 'ROLE_DIACONATE',
                'ROLE_ELDER'     => 'ROLE_ELDER',
                'ROLE_DIRECTOR'  => 'ROLE_DIRECTOR',
            ],
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
