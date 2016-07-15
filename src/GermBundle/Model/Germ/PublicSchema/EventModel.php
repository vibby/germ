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

    public function getEvents(\DateTime $from = null, \DateTime $to = null, $limit = 20)
    {
        if (!$from) {
            $from = new \DateTime;
        }
        if (!$to) {
            $to = $from;
            $to = $to->add(new \DateInterval('P1Y'));
        }


    }
}
