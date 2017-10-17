<?php

use Phinx\Migration\AbstractMigration;

class PersonSample extends AbstractMigration
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
            INSERT INTO "person"."person" (id_person_person, lastname, firstname, phone, address, email, latlong, roles) VALUES
                ('66de0f5f-8e5c-497a-8643-b94393c8a899','Paul','Less', '{}', '1 rue du calvaire\n44000 Nantes', 'paul@example.com', '( 47.215767 , -1.5613887 )','{}'),
                ('1e018920-dbf3-42d9-8e69-fb79be6689c9','Jean','Victor', '{"01 23 45 67 89"}', '1 place Graslin\n44000 Nantes', 'jeanjean@example.com', '( 47.2131678 , -1.5639745 )','{"ROLE_ADMIN"}');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "person"."church_link" (person_id, church_id) VALUES
                ('66de0f5f-8e5c-497a-8643-b94393c8a899','1d481d08-744e-48c8-92ea-375a652449eb'),
                ('1e018920-dbf3-42d9-8e69-fb79be6689c9','1d481d08-744e-48c8-92ea-375a652449eb');
SQL
        );

        // Password is «test»
        $this->execute( <<<SQL
            INSERT INTO "person"."account" ("id_person_account", "username", "username_canonical", "email", "email_canonical", "enabled", "salt", "password", "last_login", "locked", "expired", "expires_at", "confirmation_token", "password_requested_at", "credentials_expired", "credentials_expire_at", "person_id") VALUES
            ('3baf02ee-8777-4549-96cd-75efc4ec06c4', 'paolo', 'paolo', 'paolo@example.com', 'paolo@example.com', 't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   'f',    NULL,  '66de0f5f-8e5c-497a-8643-b94393c8a899'),
            ('8760965c-0a95-4d58-802a-f028b2463da3', 'jojo',  'jojo',  'jojo@example.com',  'jojo@example.com',  't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   'f',    NULL,  '1e018920-dbf3-42d9-8e69-fb79be6689c9');
SQL
        );
    }

    public function down()
    {
        $this->execute(<<<SQL
          DELETE FROM "person"."account" WHERE id_person_account in (
            '8760965c-0a95-4d58-802a-f028b2463da3',
            '3baf02ee-8777-4549-96cd-75efc4ec06c4'
          );
SQL
        );

        $this->execute(<<<SQL
          DELETE FROM "person"."person" WHERE id_person_person in (
              '66de0f5f-8e5c-497a-8643-b94393c8a899',
              '1e018920-dbf3-42d9-8e69-fb79be6689c9'
          );
SQL
        );
    }
}
