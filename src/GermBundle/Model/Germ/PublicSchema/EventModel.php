<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Event as EventStructure;
use GermBundle\Model\Germ\PublicSchema\Event;

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
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Event';
    }

    public function getEventById($eventId)
    {
        $eventTypeModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\EventTypeModel')
            ;
        $locationModel = $this
            ->getSession()
            ->getModel('\GermBundle\Model\Germ\PublicSchema\LocationModel')
            ;

        $sql = <<<SQL
select
    {projection}
from
    {event}
    inner join {eventType} et using (id)
    inner join {location} l using (id)
where
    event.id = $*
SQL;

        $projection = $this->createProjection()
            ->setField("event_type_name", "et.name as event_type_name", "varchar")
            ->setField("event_type_layout", "et.event_layout as event_type_layout", "json")
            ->setField("location_name", "l.name as location_name", "varchar")
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
}
