<?php
/**
 * This file has been automatically generated by Pomm's generator.
 * You MIGHT NOT edit this file as your changes will be lost at next
 * generation.
 */

namespace GermBundle\Model\Germ\PersonSchema\AutoStructure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Person
 *
 * Structure class for relation person.person.
 *
 * Class and fields comments are inspected from table and fields comments.
 * Just add comments in your database and they will appear here.
 * @see http://www.postgresql.org/docs/9.0/static/sql-comment.html
 *
 *
 *
 * @see RowStructure
 */
class Person extends RowStructure
{
    /**
     * __construct
     *
     * Structure definition.
     *
     * @access public
     */
    public function __construct()
    {
        $this
            ->setRelation('person.person')
            ->setPrimaryKey(['id_person_person'])
            ->addField('id_person_person', 'uuid')
            ->addField('family_id', 'int4')
            ->addField('firstname', 'varchar')
            ->addField('lastname', 'varchar')
            ->addField('roles', 'varchar[]')
            ->addField('phone', 'varchar[]')
            ->addField('address', 'varchar')
            ->addField('email', 'varchar')
            ->addField('birthdate', 'date')
            ->addField('latlong', 'point')
            ;
    }
}
