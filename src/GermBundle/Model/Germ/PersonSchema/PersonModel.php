<?php

namespace GermBundle\Model\Germ\PersonSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\Foundation\Where;

use GermBundle\Model\Germ\PersonSchema\AutoStructure\Person as PersonStructure;
use GermBundle\Model\Germ\PersonSchema\Person;

use GermBundle\Person\Searcher;

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

    public function searchQuery(Searcher $searcher, $item_per_page, $page = 1)
    {
        $where = Where::create();
        $projection = $this->createProjection();
        $projectionValues = [];
        $orderBy = ['lastname', 'firstname'];
        foreach ($searcher->getItems() as $item) {
            $where->andWhere($item->buildWhere());
            $projectionValues += $item->alterProjection($projection);
            $item->alterOrderBy($orderBy);
        }
        $count = $this->countWhere($where);

        return [
            $count,
            $this->paginateQuery(
                $this->getFindWhereSql($where, $projection, 'order by '.implode(', ', $orderBy)),
                array_merge($projectionValues, $where->getValues()),
                $count,
                $item_per_page,
                $page
            )
        ];
    }
}
