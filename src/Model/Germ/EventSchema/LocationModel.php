<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\EventSchema\AutoStructure\Location as LocationStructure;
use GermBundle\Model\Germ\EventSchema\Location;

/**
 * LocationModel
 *
 * Model class for table location.
 *
 * @see Model
 */
class LocationModel extends Model
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
        $this->structure = new LocationStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\EventSchema\Location';
    }
}
