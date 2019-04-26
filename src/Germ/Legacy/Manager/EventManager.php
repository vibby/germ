<?php

namespace Germ\Legacy\Manager;

use PommProject\Foundation\Pomm;

/**
 * EventManager
 *
 * Manager to get events
 */
class EventManager
{
    private $pomm;

    /**
     * set pomm
     */
    public function __construct(Pomm $pomm)
    {
        $this->pomm = $pomm;
    }

    public function getEvents(\DateTime $from = null, $limit = 20)
    {
        if (!$from) {
            $from = new \DateTime;
        }

        $eventModel = $this->pomm['germ']->getModel('Germ\Legacy\Model\Germ\EventSchema\EventModel');
        $eventTypeModel = $this->pomm['germ']->getModel('Germ\Legacy\Model\Germ\EventSchema\EventTypeModel');
    }
}
