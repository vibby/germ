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

        $this->execute('CREATE TABLE "docket" (
            id SERIAL PRIMARY KEY,
            name VARCHAR(32) NOT NULL,
            role VARCHAR(32)[] NULL,
            event_type_id INTEGER NOT NULL
        );');
        $this->execute('ALTER TABLE "docket" ADD FOREIGN KEY ("event_type_id") REFERENCES "event_type" ("id") ON UPDATE CASCADE;');
        $this->execute('CREATE TABLE "assignation" (
            id SERIAL PRIMARY KEY,
            person_id INTEGER NULL,
            docket_id INTEGER NULL,
            event_id INTEGER NULL,
            details JSON NULL
        );');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("person_id") REFERENCES "person" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("docket_id") REFERENCES "docket" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "assignation" ADD FOREIGN KEY ("event_id") REFERENCES "event" ("id") ON DELETE SET NULL ON UPDATE CASCADE;');

    }

    public function down()
    {
        $this->execute('DROP TABLE "assignation";');
        $this->execute('DROP TABLE "docket";');
        $this->execute('DROP TABLE "event";');
        $this->execute('DROP TABLE "event_type";');
        $this->execute('DROP TABLE "location";');
    }
}
