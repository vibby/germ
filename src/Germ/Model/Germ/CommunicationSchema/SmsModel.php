<?php

namespace Germ\Model\Germ\CommunicationSchema;

use Germ\Filter\FilterQueries;
use Germ\Model\Germ\ChurchSchema\ChurchModel;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Germ\Model\Germ\CommunicationSchema\AutoStructure\Sms as SmsStructure;

/**
 * SmsModel
 *
 * Model class for table sms.
 *
 * @see Model
 */
class SmsModel extends Model
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
        $this->structure = new SmsStructure;
        $this->flexible_entity_class = Sms::class;
    }

    public function findForListWhereSql(Where $where, $projection = null, $suffix = null)
    {
        $sql = <<<SQL
select
    :projection
from
    :sms_relation s
left join
    :church_relation ch 
    ON ch.id_church_church = s.church_id
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
                ':sms_relation' => $this->getStructure()->getRelation(),
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
