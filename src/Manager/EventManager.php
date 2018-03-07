<?php

namespace GermBundle\Manager;

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
    public function setPomm($pomm)
    {
        $this->pomm = $pomm;
    }

    public function getEvents(\DateTime $from = null, $limit = 20)
    {
        if (!$from) {
            $from = new \DateTime;
        }

        $eventModel = $this->pomm['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventModel');
        $eventTypeModel = $this->pomm['germ']->getModel('GermBundle\Model\Germ\EventSchema\EventTypeModel');





    }
}
