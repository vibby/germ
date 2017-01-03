<?php

use Phinx\Migration\AbstractMigration;

class Event extends AbstractMigration
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
        $this->execute('CREATE SCHEMA "event"');
        $this->execute('CREATE TABLE "event"."location" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(64) NOT NULL,
            details JSON NULL
        );');
        $this->execute('CREATE TABLE "event"."event_type" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(32) NULL,
            recurence VARCHAR(32) NULL,
            event_layout JSON NULL
        );');
        $this->execute('CREATE TABLE "event"."event" (
            id SERIAL PRIMARY KEY,
            type_id INTEGER NOT NULL,
            location_id INTEGER NULL,
            name VARCHAR(32) NOT NULL,
            date_from TIMESTAMP NOT NULL,
            duration INTERVAL NOT NULL,
            is_deleted BOOLEAN NULL,
            description TEXT NULL
        );');
        $this->execute('ALTER TABLE "event"."event" ADD FOREIGN KEY ("type_id") REFERENCES "event"."event_type" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."event" ADD FOREIGN KEY ("location_id") REFERENCES "event"."location" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

        $this->execute('CREATE TABLE "event"."docket" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(32) NOT NULL,
            role VARCHAR(32)[] NULL,
            event_type_id INTEGER NOT NULL
        );');
        $this->execute('ALTER TABLE "event"."docket" ADD FOREIGN KEY ("event_type_id") REFERENCES "event"."event_type" ("id") ON UPDATE CASCADE;');
        $this->execute('CREATE TABLE "event"."assignation" (
            id SERIAL PRIMARY KEY,
            account_id INTEGER NULL,
            docket_id INTEGER NULL,
            event_id INTEGER NULL,
            details JSON NULL
        );');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("account_id") REFERENCES "person"."account" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("docket_id") REFERENCES "event"."docket" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("event_id") REFERENCES "event"."event" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

    }

    public function down()
    {
        $this->execute('DROP TABLE "event"."assignation";');
        $this->execute('DROP TABLE "event"."docket";');
        $this->execute('DROP TABLE "event"."event";');
        $this->execute('DROP TABLE "event"."event_type";');
        $this->execute('DROP TABLE "event"."location";');
    }
}