<?php

use Phinx\Migration\AbstractMigration;

class EventData extends AbstractMigration
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
            INSERT INTO "event"."location" (id, name, details) VALUES
            (1, 'Churchill', '{"geolocalisation": {"latitude": "47.2128399", "longitude": "-1.5612279"}}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."event_type" (id, name, recurence, event_layout) VALUES
            (1, 'Celebration', '0 11 * * 6', '{"name": "Name sample"}'::json),
            (2, 'Event Type 2', '30 9 * * 6', '{}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."event" (id, type_id, location_id, name, date_from, duration, description) VALUES
            (1, 1, 1, 'Celebration on august the 6th, 2016', '2016-08-06 11:00:00'::timestamp, '01:30:00'::interval, ''),
            (2, 2, 1, 'Event 2', '2016-08-06 09:30:00'::timestamp, '01:15:00'::interval, '');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."docket" (id, name, role, event_type_id) VALUES
            (1, 'Predicator', '{"ROLE_PASTOR", "ROLE_ELDER"}', 1),
            (2, 'President', '{"ROLE_PRESIDENT"}', 1),
            (3, 'Prayer', '{}', 1),
            (4, 'Storyteller', '{"ROLE_STORYTELLER"}', 1),
            (5, 'President', '{"ROLE_PRESIDENT"}', 2),
            (6, 'Prayer', '{}', 2);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."assignation" (id, account_id, docket_id, event_id, details) VALUES
            (1, 1, 1, 1, '{}'::json),
            (2, 2, 2, 1, '{}'::json),
            (3, 1, 3, 1, '{}'::json),
            (4, 2, 5, 2, '{}'::json),
            (5, 1, 6, 2, '{}'::json);
SQL
        );
    }

    public function down()
    {
        $this->execute('DELETE FROM "event"."location" WHERE id in (1);');
        $this->execute('DELETE FROM "event"."event_type" WHERE id in (1,2);');
        $this->execute('DELETE FROM "event"."event" WHERE id in (1,2);');
        $this->execute('DELETE FROM "event"."docket" WHERE id in (1,6);');
        $this->execute('DELETE FROM "event"."assignation" WHERE id in (1,5);');
    }
}
