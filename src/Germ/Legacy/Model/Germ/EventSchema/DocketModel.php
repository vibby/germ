<?php

namespace Germ\Legacy\Model\Germ\EventSchema;

use Germ\Legacy\Model\Germ\EventSchema\AutoStructure\Docket as DocketStructure;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * DocketModel.
 *
 * Model class for table docket.
 *
 * @see Model
 */
class DocketModel extends Model
{
    use WriteQueries;

    /**
     * __construct().
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new DocketStructure();
        $this->flexible_entity_class = '\Germ\Legacy\Model\Germ\EventSchema\Docket';
    }

    public function getDocketsAndAssignationsForEvent(Event $event)
    {
        if (! isset($event['id_event_event'])) {
            return $this->findWhere('event_type_id = $*', [$event['type_id']]);
        }

        $assignationModel = $this
            ->getSession()
            ->getModel('\Germ\Legacy\Model\Germ\EventSchema\AssignationModel')
            ;
        $accountModel = $this
            ->getSession()
            ->getModel('\Germ\Legacy\Model\Germ\PersonSchema\AccountModel')
            ;
        $personModel = $this
            ->getSession()
            ->getModel('\Germ\Legacy\Model\Germ\PersonSchema\PersonModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {docket} d
    left outer join {assignation} ass on (ass.docket_id = d.id_event_docket AND ass.event_id = $*)
    left join {person} p on (p.id_person_person = ass.person_id)
where
    d.event_type_id = $*
SQL;

        $projection = $this->createProjection()
            ->setField('person_name', "concat(p.lastname, ' ', p.firstname) as person_name", 'varchar')
            ->setField('person_id', 'p.id_person_person as id_person_person', 'varchar')
            ->setField('person_roles', 'p.roles as person_roles', 'varchar')
            ;

        $sql = strtr(
            $sql,
            [
                '{docket}' => $this->structure->getRelation(),
                '{assignation}' => $assignationModel->getStructure()->getRelation(),
                '{person}' => $personModel->getStructure()->getRelation(),
                '{account}' => $accountModel->getStructure()->getRelation(),
                '{projection}' => $projection->formatFields('d'),
            ]
        );

        return $this->query($sql, [$event->getId(), $event->getTypeId()], $projection);
    }
}
