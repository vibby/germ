<?php

namespace GermBundle\Model\Germ\PersonSchema;

use GermBundle\Filter\FilterQueries;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use GermBundle\Model\Germ\PersonSchema\AutoStructure\Person as PersonStructure;

/**
 * PersonModel
 *
 * Model class for table person.
 *
 * @see Model
 */
class PersonModel extends Model
{
    use WriteQueries;
    use FilterQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new PersonStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PersonSchema\Person';
    }

    public function getDefaultOrderBy()
    {
        return ['lastname', 'firstname'];
    }
}
