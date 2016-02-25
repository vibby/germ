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
        $this->execute('CREATE TABLE "person" (
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
            family_id integer NULL,
            firstname VARCHAR(32) NULL,
            lastname VARCHAR(32) NOT NULL,
            phone VARCHAR(32) NULL,
            address VARCHAR(255) NULL
        );');

        $this->execute('ALTER TABLE "person" ADD FOREIGN KEY ("family_id") REFERENCES "person" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

        $this->execute( <<<SQL
            INSERT INTO "person" ("id", "username", "username_canonical", "email", "email_canonical", "enabled", "salt", "password", "last_login", "locked", "expired", "expires_at", "confirmation_token", "password_requested_at", "roles", "credentials_expired", "credentials_expire_at", firstname, lastname) VALUES
            (1, 'test', 'test', 'tast@example.com', 'test@example.com', 't',    '', 'ee26b0dd4af7e749aa1a8ee3c10ae9923f618980772e473f8819a5d4940e0db27ac185f8a0e1d5f84f88bc887fd67b143732c304cc5fa9ad8e6f57f50028a8ff', NULL,   'f',    'f',    NULL,   NULL,   NULL,   '{"ROLE_ADMIN"}',   'f',    NULL, 'Pierre', 'Kephas');
SQL
        );

    }

    public function down()
    {
        $this->execute('DROP TABLE "person";');

    }
}
