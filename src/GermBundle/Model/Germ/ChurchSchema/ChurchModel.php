<?php

namespace GermBundle\Model\Germ\ChurchSchema;

use GermBundle\Filter\FilterQueries;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;
use PommProject\Foundation\Where;
use GermBundle\Model\Germ\ChurchSchema\AutoStructure\Church as ChurchStructure;

/**
 * ChurchModel
 *
 * Model class for table church.
 *
 * @see Model
 */
class ChurchModel extends Model
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
        $this->structure = new ChurchStructure;
        $this->flexible_entity_class = '\GermBundle\Model\Germ\ChurchSchema\Church';
    }

    public function findAll($suffix = '')
    {
        return $this->traitFindAll(sprintf(
            'order By %s %s',
            implode(',', $this->getDefaultOrderBy()),
            $suffix
        ));
    }

    public function choiceSlug()
    {
        $projection = new Projection(Church::class, ['name' => 'name', 'slug_canonical' => 'slug', 'id_church_church' => 'id_church_church']);
        $churches = $this->query(strtr(
            'select :projection from :relation where true order by name',
            [
                ':projection' => $projection->formatFieldsWithFieldAlias(),
                ':relation'   => $this->getStructure()->getRelation(),
            ]
        ));

        $choices = [];
        foreach ($churches as $church) {
            $choices[$church['name']] = $church['slug_canonical'];
        }

        return $choices;
    }

    public function choiceId()
    {
        $projection = new Projection(Church::class, ['name' => 'name', 'slug_canonical' => 'slug', 'id_church_church' => 'id_church_church']);
        $churches = $this->query(strtr(
            'select :projection from :relation where true order by name',
            [
                ':projection' => $projection->formatFieldsWithFieldAlias(),
                ':relation'   => $this->getStructure()->getRelation(),
            ]
        ));

        $choices = [];
        foreach ($churches as $church) {
            $choices[$church['name']] = $church['id_church_church'];
        }

        return $choices;
    }

    public function findIdsFromSlugs(array $slugs)
    {
        $projection = new Projection(Church::class, ['id_church_church' => 'id_church_church']);
        $where = Where::createWhereIn('slug_canonical', $slugs);
        $churches = $this->query(strtr(
            'select :projection from :relation where :where',
            [
                ':projection' => $projection->formatFieldsWithFieldAlias(),
                ':relation'   => $this->getStructure()->getRelation(),
                ':where'      => $where,
            ]
        ), $where->getValues());

        $ids = [];
        foreach ($churches as $church) {
            $ids[] = $church['id_church_church'];
        }

        return $ids;
    }
}
