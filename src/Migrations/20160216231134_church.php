<?php

namespace Germ\Migrations;

use Phinx\Migration\AbstractMigration;

class Church extends AbstractMigration
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
        $this->execute('CREATE SCHEMA "church"');
        $this->execute('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        $this->execute('CREATE TABLE "church"."church" (
            id_church_church uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
            name VARCHAR(32) NOT NULL,
            slug VARCHAR(32) NOT NULL UNIQUE,
            phone VARCHAR(32) NULL,
            address VARCHAR(256) NULL,
            latlong point,
            website_url VARCHAR(128)
        );');

        $this->execute(<<<SQL
            CREATE OR REPLACE FUNCTION "church".church_slug()
            RETURNS trigger AS $$
            DECLARE newslug varchar(48); suffix int := 0;
            BEGIN
                IF NEW.slug IS NULL or NEW.slug='' THEN
                    newslug = public.slugify(NEW.name);
                    IF ((SELECT COUNT(*) FROM church.church c WHERE c.slug = newslug) = 0) THEN
                        NEW.slug = newslug;
                        RETURN NEW;
                    END IF;
                    LOOP
                        IF ((SELECT COUNT(*) FROM church.church c WHERE c.slug = newslug || suffix) = 0) THEN
                            NEW.slug = newslug || suffix;
                            RETURN NEW;
                        END IF;
                        suffix := suffix + 1;
                    END LOOP;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
SQL
        );

        $this->execute(<<<SQL
            CREATE TRIGGER "church_slug"
              BEFORE INSERT OR UPDATE
              ON "church"."church"
              FOR EACH ROW
              EXECUTE PROCEDURE "church".church_slug();
SQL
        );

    }

    public function down()
    {
        $this->execute('DROP TABLE "church"."church";');
        $this->execute('DROP FUNCTION "church".church_slug();');
        $this->execute('DROP SCHEMA "church";');
    }
}
