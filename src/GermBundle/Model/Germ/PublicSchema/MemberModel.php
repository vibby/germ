<?php

namespace GermBundle\Model\Germ\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PublicSchema\AutoStructure\Member as MemberStructure;
use GermBundle\Model\Germ\PublicSchema\Member;

/**
 * MemberModel
 *
 * Model class for table member.
 *
 * @see Model
 */
class MemberModel extends Model
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
        $this->structure = new MemberStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\PublicSchema\Member';
    }

    public function generateWhere()
    {
        return new Where(null);
    }
}
