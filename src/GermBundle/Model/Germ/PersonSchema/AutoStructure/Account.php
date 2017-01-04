<?php
/**
 * This file has been automatically generated by Pomm's generator.
 * You MIGHT NOT edit this file as your changes will be lost at next
 * generation.
 */

namespace GermBundle\Model\Germ\PersonSchema\AutoStructure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Account
 *
 * Structure class for relation person.account.
 *
 * Class and fields comments are inspected from table and fields comments.
 * Just add comments in your database and they will appear here.
 * @see http://www.postgresql.org/docs/9.0/static/sql-comment.html
 *
 *
 *
 * @see RowStructure
 */
class Account extends RowStructure
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
            ->setRelation('person.account')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('username', 'varchar')
            ->addField('username_canonical', 'varchar')
            ->addField('email', 'varchar')
            ->addField('email_canonical', 'varchar')
            ->addField('enabled', 'bool')
            ->addField('salt', 'varchar')
            ->addField('password', 'varchar')
            ->addField('last_login', 'timestamp')
            ->addField('locked', 'bool')
            ->addField('expired', 'bool')
            ->addField('expires_at', 'timestamp')
            ->addField('confirmation_token', 'varchar')
            ->addField('password_requested_at', 'timestamp')
            ->addField('credentials_expired', 'bool')
            ->addField('credentials_expire_at', 'timestamp')
            ->addField('person_id', 'int4')
            ;
    }
}
