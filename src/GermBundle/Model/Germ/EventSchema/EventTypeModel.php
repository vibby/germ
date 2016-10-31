<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\EventSchema\AutoStructure\EventType as EventTypeStructure;
use GermBundle\Model\Germ\EventSchema\EventType;

/**
 * EventTypeModel
 *
 * Model class for table event_type.
 *
 * @see Model
 */
class EventTypeModel extends Model
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
        $this->structure = new EventTypeStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\EventSchema\EventType';
    }
}
