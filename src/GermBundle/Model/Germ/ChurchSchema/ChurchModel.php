<?php

namespace GermBundle\Model\Germ\ChurchSchema;

use GermBundle\Filter\FilterQueries;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\ReadQueries;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\Foundation\Where;
use GermBundle\Model\Germ\ChurchSchema\AutoStructure\Church as ChurchStructure;
use GermBundle\Model\Germ\ChurchSchema\Church;

/**
 * ChurchModel
 *
 * Model class for table church.
 *
 * @see Model
 */
class ChurchModel extends Model
{
    use ReadQueries;
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
        $this->structure = new ChurchStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\ChurchSchema\Church';
    }

    public function getDefaultOrderBy()
    {
        return ['name'];
    }

    public function findAll($suffix = '')
    {
        return $this->traitFindAll(sprintf(
            'order By %s %s',
            implode(',', $this->getDefaultOrderBy()),
            $suffix
        ));
    }
}
