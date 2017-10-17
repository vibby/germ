<?php

use Phinx\Migration\AbstractMigration;

class EventSample extends AbstractMigration
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
            INSERT INTO "event"."location" (id_event_location, name, details) VALUES
            ('b902051e-d985-48ba-b908-8dc15dd4770e', 'Churchill', '{"geolocalisation": {"latitude": "47.2128399", "longitude": "-1.5612279"}}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."event_type" (id_event_event_type, name, recurence, event_layout) VALUES
            ('d934dc4d-dd68-4ff5-b9e4-f638732ec9a9', 'Celebration', '0 11 * * 6', '{"name": "Name sample"}'::json),
            ('2612ea6b-fc42-488a-83ca-3871ec6842ef', 'Event Type 2', '30 9 * * 6', '{}'::json);
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."event" (id_event_event, type_id, location_id, name, date_from, duration, description) VALUES
            ('52cd87f7-36aa-4fe3-93c1-bab811ebec5a', 'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9', 'b902051e-d985-48ba-b908-8dc15dd4770e', 'Celebration on august the 6th', '2016-08-06 11:00:00'::timestamp, '01:30:00'::interval, ''),
            ('d30e7989-677d-4878-abd6-93271810cc13', '2612ea6b-fc42-488a-83ca-3871ec6842ef', 'b902051e-d985-48ba-b908-8dc15dd4770e', 'Event 2', '2016-08-06 09:30:00'::timestamp, '01:15:00'::interval, '');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."docket" (id_event_docket, name, role, event_type_id) VALUES
            ('ca539c61-c562-4e15-a168-e83be9102f53', 'Predicator', '{"ROLE_PASTOR", "ROLE_ELDER"}', 'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9'),
            ('b504b8ae-de17-452b-b65d-9f9cf8f77642', 'President', '{"ROLE_PRESIDENT"}', 'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9'),
            ('ce6b5b7f-a91e-4eb0-ac0f-fb21a1894df7', 'Prayer', '{}', 'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9'),
            ('c4744e90-730a-4106-bc6a-169cc606afc5', 'Storyteller', '{"ROLE_STORYTELLER"}', 'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9'),
            ('1d21b7de-e1f9-4899-ab5c-dc2a306425fa', 'President', '{"ROLE_PRESIDENT"}', '2612ea6b-fc42-488a-83ca-3871ec6842ef'),
            ('a2c6f885-0c1b-4a81-934c-a12d05782cda', 'Prayer', '{}', '2612ea6b-fc42-488a-83ca-3871ec6842ef');
SQL
        );
        $this->execute( <<<SQL
            INSERT INTO "event"."assignation" (person_id, docket_id, event_id, details) VALUES
            ('66de0f5f-8e5c-497a-8643-b94393c8a899', 'ca539c61-c562-4e15-a168-e83be9102f53', '52cd87f7-36aa-4fe3-93c1-bab811ebec5a', '{}'::json),
            ('1e018920-dbf3-42d9-8e69-fb79be6689c9', 'b504b8ae-de17-452b-b65d-9f9cf8f77642', '52cd87f7-36aa-4fe3-93c1-bab811ebec5a', '{}'::json),
            ('66de0f5f-8e5c-497a-8643-b94393c8a899', 'ce6b5b7f-a91e-4eb0-ac0f-fb21a1894df7', '52cd87f7-36aa-4fe3-93c1-bab811ebec5a', '{}'::json),
            ('1e018920-dbf3-42d9-8e69-fb79be6689c9', '1d21b7de-e1f9-4899-ab5c-dc2a306425fa', 'd30e7989-677d-4878-abd6-93271810cc13', '{}'::json),
            ('66de0f5f-8e5c-497a-8643-b94393c8a899', 'a2c6f885-0c1b-4a81-934c-a12d05782cda', 'd30e7989-677d-4878-abd6-93271810cc13', '{}'::json);
SQL
        );
    }

    public function down()
    {
        $this->execute(<<<SQL
            DELETE FROM "event"."assignation" WHERE event_id in (
                '52cd87f7-36aa-4fe3-93c1-bab811ebec5a',
                'd30e7989-677d-4878-abd6-93271810cc13'
            );
SQL
        );
        $this->execute(<<<SQL
            DELETE FROM "event"."docket" WHERE event_type_id in (
                'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9',
                '2612ea6b-fc42-488a-83ca-3871ec6842ef'
            );
SQL
        );
        $this->execute(<<<SQL
            DELETE FROM "event"."event" WHERE id_event_event in (
                '52cd87f7-36aa-4fe3-93c1-bab811ebec5a',
                'd30e7989-677d-4878-abd6-93271810cc13'
            );
SQL
        );
        $this->execute(<<<SQL
            DELETE FROM "event"."event_type" WHERE id_event_event_type in (
                'd934dc4d-dd68-4ff5-b9e4-f638732ec9a9',
                '2612ea6b-fc42-488a-83ca-3871ec6842ef'
            );
SQL
        );
        $this->execute(<<<SQL
            DELETE FROM "event"."location" WHERE id_event_location in (
                'b902051e-d985-48ba-b908-8dc15dd4770e'
            );
SQL
        );
    }
}
