<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\EventSchema\AutoStructure\Docket as DocketStructure;
use GermBundle\Model\Germ\EventSchema\Docket;

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
        $this->flexible_entity_class = '\GermBundle\Model\Germ\EventSchema\Docket';
    }

    public function getDocketsAndAssignationsForEvent($event)
    {
        $assignationModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\EventSchema\AssignationModel')
            ;
        $accountModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PersonSchema\AccountModel')
            ;
        $personModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PersonSchema\PersonModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {docket} d
    left outer join {assignation} ass on (ass.docket_id = d.id AND ass.event_id = $*)
    left join {account} acc on (acc.id = ass.account_id)
    left join {person} p on (p.id = acc.person_id)
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

        return $this->query($sql, [$event->getId(), $event->getTypeId()], $projection);
    }
}
