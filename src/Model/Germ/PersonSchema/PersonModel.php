<?php

namespace GermBundle\Model\Germ\PersonSchema;

use GermBundle\Filter\FilterQueries;
use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use GermBundle\Model\Germ\PersonSchema\AutoStructure\Person as PersonStructure;
use PommProject\ModelManager\Model\Projection;

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

    public function findForListWhere(Where $where)
    {
        list($sql, $projection) = $this->findForListWhereSql($where);

        return $this->query($sql, $where->getValues(), $projection);
    }

    public function findForListWhereSql(Where $where, $projection = null, $suffix = null)
    {
        $sql = <<<SQL
select
    :projection
from
    :person_relation p
right join
    :church_relation c 
    ON c.id_church_church = p.church_id
where
    :condition
:suffix
SQL;

        if (!$projection) {
            $projection = new Projection(
                $this->flexible_entity_class,
                $this->structure->getDefinition() //$structure
            );
        }

        $projection->setField('church_name', 'c.name', 'varchar');
        $projection->setField('church_slug', 'c.slug', 'varchar');
        $projection->unsetField('slug');
        $projection->setField('slug', 'p.slug', 'varchar');
        $projection->unsetField('phone');
        $projection->setField('phone', 'p.phone', 'varchar');
        $projection->unsetField('address');
        $projection->setField('address', 'p.address', 'varchar');
        $projection->unsetField('latlong');
        $projection->setField('latlong', 'p.latlong', 'point');

        $sql = strtr($sql,
            [
                ':projection'      => $projection,
                ':person_relation' => $this->getStructure()->getRelation(),
                ':church_relation' => $this->getSession()
                    ->getModel('\GermBundle\Model\Germ\ChurchSchema\ChurchModel')
                    ->getStructure()
                    ->getRelation(),
                ':condition' => $where,
                ':suffix'    => $suffix,
            ]
        );

        return [$sql, $projection];
    }
}
