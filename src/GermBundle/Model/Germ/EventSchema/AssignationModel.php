<?php

namespace GermBundle\Model\Germ\EventSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\EventSchema\AutoStructure\Assignation as AssignationStructure;
use GermBundle\Model\Germ\EventSchema\Assignation;

/**
 * AssignationModel
 *
 * Model class for table assignation.
 *
 * @see Model
 */
class AssignationModel extends Model
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
        $this->structure = new AssignationStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\EventSchema\Assignation';
    }
}