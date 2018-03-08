<?php

namespace Germ\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\ModelManager\Model\ModelTrait\ReadQueries;

use PommProject\Foundation\Where;

use Germ\Model\Germ\EventSchema\AutoStructure\EventType as EventTypeStructure;
use Germ\Model\Germ\EventSchema\EventType;

/**
 * EventTypeModel
 *
 * Model class for table event_type.
 *
 * @see Model
 */
class EventTypeModel extends Model
{
    use ReadQueries;

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
        $this->flexible_entity_class = '\Germ\Model\Germ\EventSchema\EventType';
    }
}
