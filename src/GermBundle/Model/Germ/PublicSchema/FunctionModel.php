<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Function as FunctionStructure;
use GermBundle\Model\Germ\PublicSchema\Function;

/**
 * FunctionModel
 *
 * Model class for table function.
 *
 * @see Model
 */
class FunctionModel extends Model
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
        $this->structure = new FunctionStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Function';
    }
}
