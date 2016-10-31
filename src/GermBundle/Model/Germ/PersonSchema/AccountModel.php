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
        $personModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PersonSchema\PersonModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {account} a
    inner join {person} p on (p.id = a.person_id)
where
    {where}
SQL;


        $projection = $this->createProjection()
            ->setField("person_name", "concat(p.lastname, ' ', p.firstname) as person_name", "varchar")
            ->setField("account_id", "a.id as account_id", "varchar")
            ;
        $where = new Where();
        if ($role) {
            $where->andWhereIn('role', $role);
        }

        $sql = strtr(
            $sql,
            [
                '{account}'     => $this->structure->getRelation(),
                '{person}'      => $personModel->getStructure()->getRelation(),
                '{projection}'  => $projection->formatFields('a'),
                '{where}'       => $where,
            ]
        );


        return $this->query($sql, [], $projection);
    }
}
