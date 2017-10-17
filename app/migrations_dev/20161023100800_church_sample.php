<?php

use Phinx\Migration\AbstractMigration;

class ChurchSample extends AbstractMigration
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
            INSERT INTO "church"."church" (
                id_church_church,
                name,
                slug_canonical,
                phone,
                address,
                latlong,
                website_url
            ) VALUES (
                '1d481d08-744e-48c8-92ea-375a652449eb',
                'Nantes',
                'nantes',
                '02 40 49 65 18',
                '42 Boulevard Auguste Peneau\n44300 Nantes',
                '( 47.232088, -1.5070572 )',
                'http://www.nantes-adventiste.com/'
            ),(
                '268d77ba-8b06-492a-a63f-e8ca54f0156d',
                'Rennes',
                'rennes',
                '02 99 54 53 30',
                '19 Boulevard Marbeuf\n35000 Rennes',
                '( 48.1103891, -1.7062643 )',
                'http://rennes.adventiste.org/'
            );
SQL
        );
    }

    public function down()
    {
        $this->execute(<<<SQL
          DELETE FROM "church"."church" WHERE id_church_church in (
            '1d481d08-744e-48c8-92ea-375a652449eb',
            '268d77ba-8b06-492a-a63f-e8ca54f0156d'
          );
SQL
        );
    }
}
