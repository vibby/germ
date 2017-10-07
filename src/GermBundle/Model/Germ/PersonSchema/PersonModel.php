<?php

namespace GermBundle\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PersonSchema\AutoStructure\Person as PersonStructure;
use GermBundle\Model\Germ\PersonSchema\Person;

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

    public function getPersons(Array $role = [])
    {
        $where = new Where();
        if ($role) {
            $where->andWhereIn('role', $role);
        }
        return $this->findWhere($where);
    }

    public function searchQuery(array $terms, $item_per_page, $page = 1)
    {
        $where = Where::create();
        foreach ($terms as $searchTerm) {
            $where->orWhere(Where::createGroupCondition('LOWER(lastname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]));
            $where->orWhere(Where::createGroupCondition('LOWER(firstname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]));
        }

        $projection = $this->createProjection();
        $field = '';
        $countResultProjectionValues = [];
        foreach ($terms as $searchTerm) {
            $field .= $field ? ' + ' : '(';
            $condition = (string) Where::createGroupCondition('LOWER(lastname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]);
            $countResultProjectionValues[] = sprintf('%s%%', strtolower($searchTerm));
            $field .= 'case when '.$condition.' THEN 1 ELSE 0 END';
            $field .= ' + ';
            $condition = (string) Where::createGroupCondition('LOWER(firstname)', 'LIKE', [sprintf('%s%%', strtolower($searchTerm))]);
            $countResultProjectionValues[] = sprintf('%s%%', strtolower($searchTerm));
            $field .= 'case when '.$condition.' THEN 1 ELSE 0 END';
        }
        $fieldName = 'countresults';
        $projection->setField($fieldName, $field.')');

        $orderBy = "order by $fieldName DESC, lastname, firstname";
        $count = $this->countWhere($where);

        return [
            $count,
            $this->paginateQuery(
                $this->getFindWhereSql($where, $projection, $orderBy),
                array_merge($countResultProjectionValues, $where->getValues()),
                $count,
                $item_per_page,
                $page
            )
        ];
    }
}
