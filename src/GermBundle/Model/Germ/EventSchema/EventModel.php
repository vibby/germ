<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\EventSchema\AutoStructure\Event as EventStructure;
use GermBundle\Model\Germ\EventSchema\Event;

/**
 * EventModel
 *
 * Model class for table event.
 *
 * @see Model
 */
class EventModel extends Model
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
        $this->structure = new EventStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\EventSchema\Event';
    }

    public function hydrateDockets(Event $event)
    {
        $docketModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\EventSchema\DocketModel')
            ;
        $event->setDockets($docketModel->getDocketsAndAssignationsForEvent($event));

        return $event;
    }

    public function getEventById($eventId)
    {
        $eventTypeModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\EventSchema\EventTypeModel')
            ;
        $locationModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\EventSchema\LocationModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {event}
    inner join {eventType} et on (et.id_event_event_type = event.type_id)
    left join {location} lo on (lo.id_event_location = event.location_id)
where
    event.id_event_event = $*
SQL;

        $projection = $this->createProjection()
            ->setField("event_type_name", "et.name as event_type_name", "varchar")
            ->setField("event_type_layout", "et.event_layout as event_type_layout", "json")
            ->setField("location_name", "lo.name as location_name", "varchar")
            ;

        $sql = strtr(
            $sql,
            [
                '{event}'      => $this->structure->getRelation(),
                '{eventType}'  => $eventTypeModel->getStructure()->getRelation(),
                '{location}'   => $locationModel->getStructure()->getRelation(),
                '{projection}' => $projection->formatFields('event'),
            ]
        );

        return $this->query($sql, [$eventId], $projection)->current();
    }

    public function saveEvent(Event $event, array $properties)
    {
        $this->startTransaction();
        try {
            foreach ($event->dockets as $docket) {
                $this->save($event, $properties);
                $assignationModel = $this->getModel(assignationModel::class)
                    ->updateOne(
                        [
                            'account_id' => $docket['target'],
                            'event_id' => $event->getId(),
                            'docket_id' => $docket['id_event_docket'],
                        ]
                    );
            }
        } catch (\Exception $e) {
            // If an exception is thrown, rollback everything and propage it.
            $this->rollbackTransaction();

            throw $e;
        }
    }
}
