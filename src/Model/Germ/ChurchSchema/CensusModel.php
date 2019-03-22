<?php

namespace Germ\Model\Germ\ChurchSchema;

use Germ\Filter\FilterQueries;
use Germ\Model\Germ\ChurchSchema\AutoStructure\Census as CensusStructure;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\ModelManager\Model\Projection;

/**
 * CensusModel.
 *
 * Model class for table census.
 *
 * @see Model
 */
class CensusModel extends Model
{
    use WriteQueries;
    use FilterQueries;

    /**
     * __construct().
     *
     * Model constructor
     */
    public function __construct()
    {
        $this->structure = new CensusStructure();
        $this->flexible_entity_class = '\Germ\Model\Germ\ChurchSchema\Census';
    }

    public function findForListWhereSql(Where $where, $projection = null, $suffix = null)
    {
        $sql = <<<SQL
select
    :projection
from
    :census_relation c
inner join
    :church_relation ch 
    ON ch.id_church_church = c.church_id
where
    :condition
:suffix
SQL;

        if (! $projection) {
            $projection = new Projection(
                $this->flexible_entity_class,
                $this->structure->getDefinition() //$structure
            );
        }

        $projection->setField('church_name', 'ch.name', 'varchar');
        $projection->setField('church_slug', 'ch.slug', 'varchar');

        $sql = strtr(
            $sql,
            [
                ':projection' => $projection,
                ':census_relation' => $this->getStructure()->getRelation(),
                ':church_relation' => $this->getSession()
                    ->getModel(ChurchModel::class)
                    ->getStructure()
                    ->getRelation(),
                ':condition' => $where,
                ':suffix' => $suffix,
            ]
        );

        return [$sql, $projection];
    }
}
