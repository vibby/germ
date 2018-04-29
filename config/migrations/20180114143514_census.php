<?php

use Phinx\Migration\AbstractMigration;

class Census extends AbstractMigration
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
        $this->execute('CREATE TABLE "church"."census" (
            id_church_census uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            date TIMESTAMP NOT NULL,
            count INTEGER NOT NULL,
            church_id uuid REFERENCES "church"."church" NOT NULL
        );');
        $this->execute('ALTER TABLE "event"."assignation" ADD FOREIGN KEY ("event_id") REFERENCES "event"."event" ("id_event_event") ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    public function down()
    {
        $this->execute('DROP TABLE "church"."census";');
    }
}
