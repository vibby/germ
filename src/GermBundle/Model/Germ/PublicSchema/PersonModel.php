<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Person as PersonStructure;
use GermBundle\Model\Germ\PublicSchema\Person;

use Vibby\PommProjectFosUserBundle\Model\UserModel;

/**
 * PersonModel
 *
 * Model class for table person.
 *
 * @see Model
 */
class PersonModel extends UserModel
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
        $this->structure = new PersonStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Person';
    }
}
