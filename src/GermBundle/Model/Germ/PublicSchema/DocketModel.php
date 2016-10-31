<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Docket as DocketStructure;
use GermBundle\Model\Germ\PublicSchema\Docket;

/**
 * DocketModel
 *
 * Model class for table docket.
 *
 * @see Model
 */
class DocketModel extends Model
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
        $this->structure = new DocketStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Docket';
    }

    public function getDocketsAndAssignationsForEvent($event)
    {
        $assignationModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\AssignationModel')
            ;
        $accountModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\AccountModel')
            ;
        $personModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\PersonModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {docket} d
    inner join {assignation} ass using (id)
    inner join {account} acc using (id)
    inner join {person} p using (id)
where
    d.event_type_id = $*
SQL;

        $projection = $this->createProjection()
            ->setField("person_name", "concat(p.lastname, ' ', p.firstname) as person_name", "varchar")
            ->setField("account_roles", "acc.roles as account_roles", "varchar")
            ->setField("account_id", "acc.id as account_id", "varchar")
            ;

        $sql = strtr(
            $sql,
            [
                '{docket}'      => $this->structure->getRelation(),
                '{assignation}' => $assignationModel->getStructure()->getRelation(),
                '{person}'      => $personModel->getStructure()->getRelation(),
                '{account}'     => $accountModel->getStructure()->getRelation(),
                '{projection}'  => $projection->formatFields('d'),
            ]
        );

        return $this->query($sql, [$event->getTypeId()], $projection);
    }
}
