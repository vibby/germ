<?php

namespace Germ\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Germ\Model\Germ\PersonSchema\AutoStructure\PersonChurch as PersonChurchStructure;
use Germ\Model\Germ\PersonSchema\PersonChurch;

/**
 * PersonChurchModel
 *
 * Model class for table person_church.
 *
 * @see Model
 */
class PersonChurchModel extends Model
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
        $this->structure = new PersonChurchStructure;
        $this->flexible_entity_class = '\Germ\PersonSchema\PersonChurch';
    }
}
