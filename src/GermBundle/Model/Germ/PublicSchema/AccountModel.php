<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Account as AccountStructure;
use GermBundle\Model\Germ\PublicSchema\Account;

use Vibby\PommProjectFosUserBundle\Model\UserModel;

/**
 * AccountModel
 *
 * Model class for table account.
 *
 * @see Model
 */
class AccountModel extends UserModel
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
        $this->structure = new AccountStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Account';
    }
}
