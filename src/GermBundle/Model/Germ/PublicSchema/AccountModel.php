<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Account as AccountStructure;
use GermBundle\Model\Germ\PublicSchema\Account;

use Vibby\PommProjectFosUserBundle\Model\UserModel;

/**
 * AccountModel
 *
 * Model class for table account.
 *
 * @see Model
 */
class AccountModel extends UserModel
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
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Account';
    }

    public function getAccounts()
    {
        $personModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\PersonModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {account} a
    inner join {person} p using (id)
where
    true
SQL;

        $projection = $this->createProjection()
            ->setField("person_name", "concat(p.lastname, ' ', p.firstname) as person_name", "varchar")
            ->setField("account_id", "a.id as account_id", "varchar")
            ;

        $sql = strtr(
            $sql,
            [
                '{account}'     => $this->structure->getRelation(),
                '{person}'      => $personModel->getStructure()->getRelation(),
                '{projection}'  => $projection->formatFields('a'),
            ]
        );

        return $this->query($sql, [], $projection);
    }
}
