<?php

use Phinx\Migration\AbstractMigration;

class Members extends AbstractMigration
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
        $this->execute('CREATE TABLE "account" (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            username_canonical VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL,
            email_canonical VARCHAR(255) NOT NULL UNIQUE,
            enabled BOOLEAN NOT NULL DEFAULT FALSE,
            salt VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            last_login TIMESTAMP WITHOUT TIME ZONE,
            locked BOOLEAN NOT NULL DEFAULT FALSE,
            expired BOOLEAN NOT NULL DEFAULT FALSE,
            expires_at TIMESTAMP WITHOUT TIME ZONE,
            confirmation_token VARCHAR(255),
            password_requested_at TIMESTAMP WITHOUT TIME ZONE,
            roles TEXT[] NOT NULL,
            credentials_expired BOOLEAN NOT NULL DEFAULT FALSE,
            credentials_expire_at TIMESTAMP WITHOUT TIME ZONE,
            person_id integer NOT NULL
        );');
        $this->execute('CREATE TABLE "person" (
            id SERIAL PRIMARY KEY,
            family_id integer NULL,
            firstname VARCHAR(32) NULL,
            lastname VARCHAR(32) NOT NULL,
            phone VARCHAR(32)[] NULL,
            address VARCHAR(256) NULL,
            email VARCHAR(64) NULL,
            birthdate DATE NULL
        );');

        $this->execute('ALTER TABLE "person" ADD FOREIGN KEY ("family_id") REFERENCES "person" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "account" ADD FOREIGN KEY ("person_id") REFERENCES "person" ("id") ON DELETE CASCADE ON UPDATE CASCADE;');

        $this->execute( <<<SQL
            INSERT INTO "person" (id, firstname, lastname, phone, address) VALUES
            (1, 'Pierre', 'Kephas', '{"0240496518"}', '42 Boulevard Auguste Péneau\n44300 Nantes');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "person" (id, firstname, lastname) VALUES
            (2, 'Paul', 'Paulus');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "account" ("id", "username", "username_canonical", "email", "email_canonical", "enabled", "salt", "password", "last_login", "locked", "expired", "expires_at", "confirmation_token", "password_requested_at", "roles", "credentials_expired", "credentials_expire_at", "person_id") VALUES
            (1, 'pierre', 'pierre', 'pierre@example.com', 'pierre@example.com', 't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   '{"ROLE_ADMIN"}',   'f',    NULL,  1);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "account" ("id", "username", "username_canonical", "email", "email_canonical", "enabled", "salt", "password", "last_login", "locked", "expired", "expires_at", "confirmation_token", "password_requested_at", "roles", "credentials_expired", "credentials_expire_at", "person_id") VALUES
            (2, 'paul', 'paul', 'paul@example.com', 'paul@example.com', 't',    '5cmq15n0q0w0go4ogcc0co444ocw4oc', 'LJQRDmbG37bHbZTi0oTH4td8L6mHU7kecPoX2zw8SDwWFpBcT11bQqx+FjOYvfSyP8BdZhwYlUB/kTp1RR31Qg==', NULL,   'f',    'f',    NULL,   NULL,   NULL,   '{"ROLE_ELDER"}',   'f',    NULL,   2);
SQL
        );

    }

    public function down()
    {
        $this->execute('DROP TABLE "account";');
        $this->execute('DROP TABLE "person";');
    }
}
