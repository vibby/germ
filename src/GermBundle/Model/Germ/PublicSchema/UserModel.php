<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\User as UserStructure;
use GermBundle\Model\Germ\PublicSchema\User;

use Vibby\PommProjectFosUserBundle\Model\UserModel as BaseUserModel;

/**
 * UserModel
 *
 * Model class for table user.
 *
 * @see Model
 */
class UserModel extends BaseUserModel
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
        $this->structure = new UserStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\User';
    }
}
