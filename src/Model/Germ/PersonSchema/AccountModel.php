<?php

namespace Germ\Model\Germ\PersonSchema;

use Germ\Model\Germ\PersonSchema\AutoStructure\Account as AccountStructure;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * AccountModel.
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
     * __construct().
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new AccountStructure();
        $this->flexible_entity_class = '\Germ\Model\Germ\PersonSchema\Account';
    }

    public function getAccounts(array $role = [])
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
            ->setField('username', 'p.slug', 'varchar')
            ->setField('username_canonical', 'p.slug', 'varchar')
            ->setField('id_person_person', 'p.id_person_person', 'varchar')
            ;

        $sql = strtr(
            $sql,
            [
                ':projection' => $projection,
                ':user_relation' => $this->getStructure()->getRelation(),
                ':person_relation' => $this->getSession()
                    ->getModel('\Germ\Model\Germ\PersonSchema\PersonModel')
                    ->getStructure()
                    ->getRelation(),
                ':church_relation' => $this->getSession()
                    ->getModel('\Germ\Model\Germ\ChurchSchema\ChurchModel')
                    ->getStructure()
                    ->getRelation(),
                ':condition' => $where,
            ]
        );

        return $this->query($sql, $where->getValues(), $projection);
    }
}
