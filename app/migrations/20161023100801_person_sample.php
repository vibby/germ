<?php

use Phinx\Migration\AbstractMigration;

class PersonData extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->execute( <<<SQL
            INSERT INTO "person"."person" (id, lastname, firstname, phone, address, email, latlong, roles) VALUES
                (1,'Paul','Less', '{}', '1 rue du calvaire\n44000 Nantes', 'paul@example.com', '( 47.215767 , -1.5613887 )','{}'),
                (2,'Jean','Victor', '{"01 23 45 67 89"}', '1 place Graslin\n44000 Nantes', 'jeanjean@example.com', '( 47.2131678 , -1.5639745 )','{"ROLE_ADMIN"}');
SQL

        );

        // Password is «test»
        $this->execute( <<<SQL
            INSERT INTO "person"."account" ("id", "username", "username_canonical", "email", "email_canonical", "enabled", "salt", "password", "last_login", "locked", "expired", "expires_at", "confirmation_token", "password_requested_at", "credentials_expired", "credentials_expire_at", "person_id") VALUES
            (1, 'paolo', 'paolo', 'paolo@example.com', 'paolo@example.com', 't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   'f',    NULL,  1),
            (2, 'jojo', 'jojo', 'jojo@example.com', 'jojo@example.com', 't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   'f',    NULL,   2);
SQL
        );
    }

    public function down()
    {
        $this->execute('DELETE FROM "person"."account" WHERE id in (1,2);');
        $this->execute('DELETE FROM "person"."person" WHERE id in (1,2);');
    }
}
