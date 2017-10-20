<?php

namespace GermBundle\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PersonSchema\AutoStructure\Account as AccountStructure;
use GermBundle\Model\Germ\PersonSchema\Account;

/**
 * AccountModel
 *
 * Model class for table account.
 *
 * @see Model
 */
class AccountModel extends Model
{
    use WriteQueries;

    public $keyForId = 'id_person_account';

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new AccountStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PersonSchema\Account';
    }

    public function getAccounts(Array $role = [])
    {
        $where = new Where();
        if ($role) {
            $where->andWhereIn('role', $role);
        }
        return $this->findWhere($where);
    }

    public function findUserWhere(Where $where)
    {
        $sql = <<<SQL
select
    :projection
from
    :user_relation r
right join
    :person_relation p 
    ON p.id_person_person = r.person_id
    AND p.is_deleted = false
right join
    :church_relation c 
    ON c.id_church_church = p.church_id
where
    :condition
SQL;
        $projection = $this->createProjection()
            ->setField('roles', 'p.roles', 'varchar[]')
            ->setField('church_id', 'p.church_id', 'varchar')
            ->setField('church_name', 'c.name', 'varchar')
            ->setField('email', 'p.email', 'varchar')
            ->setField('username', 'p.slug_canonical', 'varchar')
            ->setField('username_canonical', 'p.slug_canonical', 'varchar')
            ->setField('id_person_person', 'p.id_person_person', 'varchar')
            ;

        $sql = strtr($sql,
            [
                ':projection'      => $projection,
                ':user_relation'   => $this->getStructure()->getRelation(),
                ':person_relation' => $this->getSession()
                    ->getModel('\GermBundle\Model\Germ\PersonSchema\PersonModel')
                    ->getStructure()
                    ->getRelation(),
                ':church_relation' => $this->getSession()
                    ->getModel('\GermBundle\Model\Germ\ChurchSchema\ChurchModel')
                    ->getStructure()
                    ->getRelation(),
                ':condition' => $where,
            ]
        );

        return $this->query($sql, $where->getValues(), $projection);
    }
}
