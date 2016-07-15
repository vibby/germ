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
            roles VARCHAR(32)[] NOT NULL,
            credentials_expired BOOLEAN NOT NULL DEFAULT FALSE,
            credentials_expire_at TIMESTAMP WITHOUT TIME ZONE,
            person_id INTEGER NOT NULL
        );');
        $this->execute('CREATE TABLE "person" (
            id SERIAL PRIMARY KEY,
            family_id INTEGER NULL,
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

        $this->execute('CREATE TABLE "location" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(64) NOT NULL,
            details JSON NULL
        );');
        $this->execute('CREATE TABLE "event_type" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(32) NULL,
            recurence VARCHAR(32) NULL,
            event_layout JSON NULL
        );');
        $this->execute('CREATE TABLE "event" (
            id SERIAL PRIMARY KEY,
            type_id INTEGER NOT NULL,
            location_id INTEGER NULL,
            name VARCHAR(32) NOT NULL,
            date_from TIMESTAMP NOT NULL,
            duration INTERVAL NOT NULL,
            is_deleted BOOLEAN NULL,
            description TEXT NULL
        );');
        $this->execute('ALTER TABLE "event" ADD FOREIGN KEY ("type_id") REFERENCES "event_type" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event" ADD FOREIGN KEY ("location_id") REFERENCES "location" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

        $this->execute( <<<SQL
            INSERT INTO "location" (id, name, details) VALUES
            (1, 'Église Adventiste de Nantes', '{"geolocalisation": {"latitude": "47.232191", "longitude": "-1.5078727"}}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event_type" (id, name, recurence, event_layout) VALUES
            (1, 'Culte', '0 11 * * 6', '{"name": "Culte du 6 août 2016"}'::json),
            (2, 'Catéchèse', '30 9 * * 6', '{}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event" (id, type_id, location_id, name, date_from, duration, description) VALUES
            (1, 1, 1, 'Culte du 6 août 2016', '2016-08-06 11:00:00'::timestamp, '01:30:00'::interval, ''),
            (2, 1, 1, 'Catéchèse du 6 août 2016', '2016-08-06 09:30:00'::timestamp, '01:15:00'::interval, '');
SQL
        );

        $this->execute('CREATE TABLE "function" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(32) NOT NULL,
            role VARCHAR(32)[] NULL
        );');
        $this->execute('CREATE TABLE "assignation" (
            id SERIAL PRIMARY KEY,
            person_id INTEGER NULL,
            function_id INTEGER NULL,
            event_id INTEGER NULL,
            details JSON NULL
        );');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("person_id") REFERENCES "person" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("function_id") REFERENCES "function" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("event_id") REFERENCES "event" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

        $this->execute( <<<SQL
            INSERT INTO "function" (id, name, role) VALUES
            (1, 'Prédicateur', '{"ROLE_PASTOR", "ROLE_ELDER"}'),
            (2, 'Président', '{"ROLE_PRESIDENT"}'),
            (3, 'Prière', '{}');
SQL
        );        $this->execute( <<<SQL
            INSERT INTO "assignation" (id, person_id, function_id, event_id, details) VALUES
            (1, 1, 1, 1, '{}'::json),
            (2, 2, 2, 1, '{}'::json),
            (3, 1, 3, 1, '{}'::json),
            (4, 2, 2, 2, '{}'::json),
            (5, 1, 3, 2, '{}'::json);
SQL
        );

    }

    public function down()
    {
        $this->execute('DROP TABLE "assignation";');
        $this->execute('DROP TABLE "function";');
        $this->execute('DROP TABLE "event";');
        $this->execute('DROP TABLE "event_type";');
        $this->execute('DROP TABLE "location";');
        $this->execute('DROP TABLE "account";');
        $this->execute('DROP TABLE "person";');
    }
}
