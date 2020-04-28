<?php

use Phinx\Migration\AbstractMigration;

class Sms extends AbstractMigration
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
        $this->execute('CREATE SCHEMA "communication"');
        $this->execute('CREATE TABLE "communication"."sms" (
            id_communication_sms uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            date TIMESTAMP NOT NULL,
            content VARCHAR(160) NOT NULL,
            status VARCHAR(32) NOT NULL,
            church_id uuid REFERENCES "church"."church" NULL
        );');
        $this->execute(
            <<<SQL
            CREATE TABLE "communication"."person_sms" (
                person_id uuid REFERENCES "person"."person",
                sms_id uuid REFERENCES "communication"."sms"
            );
SQL
        );
    }

    public function down()
    {
        $this->execute('DROP TABLE "communication"."person_sms";');
        $this->execute('DROP TABLE "communication"."sms";');
        $this->execute('DROP SCHEMA "communication"');
    }
}
