<?php

use Phinx\Migration\AbstractMigration;

class Person extends AbstractMigration
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
        $this->execute('CREATE SCHEMA "person"');
        $this->execute('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        $this->execute('CREATE TABLE "person"."account" (
            id_person_account uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
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
            credentials_expired BOOLEAN NOT NULL DEFAULT FALSE,
            credentials_expire_at TIMESTAMP WITHOUT TIME ZONE,
            person_id uuid NOT NULL
        );');
        $this->execute('CREATE TABLE "person"."person" (
            id_person_person uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            family_id uuid NULL,
            firstname VARCHAR(32) NULL,
            lastname VARCHAR(32) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(32)[] NULL,
            address VARCHAR(256) NULL,
            email VARCHAR(64) NULL,
            roles VARCHAR(32)[] NOT NULL,
            birthdate DATE NULL,
            latlong point
        );');

        $this->execute('ALTER TABLE "person"."person" ADD FOREIGN KEY ("family_id") REFERENCES "person"."person" ("id_person_person") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "person"."account" ADD FOREIGN KEY ("person_id") REFERENCES "person"."person" ("id_person_person") ON DELETE CASCADE ON UPDATE CASCADE;');

    }

    public function down()
    {
        $this->execute('DROP TABLE "person"."account";');
        $this->execute('DROP TABLE "person"."person";');
        $this->execute('DROP SCHEMA "person";');
    }
}
