<?php
/**
 * This file has been automatically generated by Pomm's generator.
 * You MIGHT NOT edit this file as your changes will be lost at next
 * generation.
 */

namespace Germ\Model\Germ\ChurchSchema\AutoStructure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Church.
 *
 * Structure class for relation church.church.
 *
 * Class and fields comments are inspected from table and fields comments.
 * Just add comments in your database and they will appear here.
 *
 * @see http://www.postgresql.org/docs/9.0/static/sql-comment.html
 * @see RowStructure
 */
class Church extends RowStructure
{
    /**
     * __construct.
     *
     * Structure definition.
     */
    public function __construct()
    {
        $this
            ->setRelation('church.church')
            ->setPrimaryKey(['id_church_church'])
            ->addField('id_church_church', 'uuid')
            ->addField('name', 'varchar')
            ->addField('slug', 'varchar')
            ->addField('phone', 'varchar')
            ->addField('address', 'varchar')
            ->addField('latlong', 'point')
            ->addField('website_url', 'varchar')
            ;
    }
}
