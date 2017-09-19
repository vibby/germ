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
        //$this->execute('CREATE SCHEMA "event"');
        $this->execute('CREATE TABLE "event"."location" (
            id_event_location uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            name VARCHAR(64) NOT NULL,
            details JSON NULL
        );');
        $this->execute('CREATE TABLE "event"."event_type" (
            id_event_event_type uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            name VARCHAR(32) NULL,
            recurence VARCHAR(32) NULL,
            event_layout JSON NULL
        );');
        $this->execute('CREATE TABLE "event"."event" (
            id_event_event uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            type_id uuid NOT NULL,
            location_id uuid NULL,
            name VARCHAR(32) NOT NULL,
            date_from TIMESTAMP NOT NULL,
            duration INTERVAL NOT NULL,
            is_deleted BOOLEAN NULL,
            description TEXT NULL
        );');
        $this->execute('ALTER TABLE "event"."event" ADD FOREIGN KEY ("type_id") REFERENCES "event"."event_type" ("id_event_event_type") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."event" ADD FOREIGN KEY ("location_id") REFERENCES "event"."location" ("id_event_location") ON DELETE SET NULL ON UPDATE CASCADE;');

        $this->execute('CREATE TABLE "event"."docket" (
            id_event_docket uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            name VARCHAR(32) NOT NULL,
            role VARCHAR(32)[] NULL,
            event_type_id uuid NOT NULL
        );');
        $this->execute('ALTER TABLE "event"."docket" ADD FOREIGN KEY ("event_type_id") REFERENCES "event"."event_type" ("id_event_event_type") ON UPDATE CASCADE;');
        $this->execute('CREATE TABLE "event"."assignation" (
            id_event_assignation uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            account_id uuid NULL,
            docket_id uuid NULL,
            event_id uuid NULL,
            details JSON NULL
        );');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("account_id") REFERENCES "person"."account" ("id_person_account") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("docket_id") REFERENCES "event"."docket" ("id_event_docket") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("event_id") REFERENCES "event"."event" ("id_event_event") ON DELETE SET NULL ON UPDATE CASCADE;');

    }

    public function down()
    {
        $this->execute('DROP TABLE "event"."assignation";');
        $this->execute('DROP TABLE "event"."docket";');
        $this->execute('DROP TABLE "event"."event";');
        $this->execute('DROP TABLE "event"."event_type";');
        $this->execute('DROP TABLE "event"."location";');
        $this->execute('DROP SCHEMA "event";');
    }
}
