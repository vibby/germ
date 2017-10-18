<?php

use Phinx\Migration\AbstractMigration;

class Person extends AbstractMigration
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
        $this->execute('CREATE SCHEMA "person"');
        $this->execute(<<<SQL
            CREATE TABLE "person"."account" (
                id_person_account uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
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
                credentials_expired BOOLEAN NOT NULL DEFAULT FALSE,
                credentials_expire_at TIMESTAMP WITHOUT TIME ZONE,
                person_id uuid NOT NULL
            );
SQL
        );
        $this->execute(<<<SQL
            CREATE TABLE "person"."person" (
                id_person_person uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
                family_id uuid NULL,
                firstname VARCHAR(32) NULL,
                lastname VARCHAR(32) NOT NULL,
                slug_canonical VARCHAR(48) NOT NULL UNIQUE,
                phone VARCHAR(32)[] NULL,
                address VARCHAR(256) NULL,
                email VARCHAR(64) NULL,
                roles VARCHAR(32)[] NOT NULL,
                birthdate DATE NULL,
                baptism_date DATE NULL,
                membership_date DATE NULL,
                membership_way VARCHAR(32) NULL,
                church_id uuid NOT NULL,
                is_deleted BOOLEAN NOT NULL DEFAULT FALSE,
                latlong point NULL
            );
SQL
        );
        $this->execute(<<<SQL
            CREATE TABLE "person"."person_church" (
                person_id uuid REFERENCES "person"."person",
                church_id uuid REFERENCES "church"."church"
            );
SQL
        );

        $this->execute('ALTER TABLE "person"."person" ADD FOREIGN KEY ("family_id") REFERENCES "person"."person" ("id_person_person") ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->execute('ALTER TABLE "person"."account" ADD FOREIGN KEY ("person_id") REFERENCES "person"."person" ("id_person_person") ON DELETE CASCADE ON UPDATE CASCADE;');

        $this->execute(<<<SQL
            CREATE OR REPLACE FUNCTION "public".slugify(str TEXT)
            RETURNS text AS $$
            BEGIN
                RETURN regexp_replace(
                   lower(translate(str,
                     'äëïöüáéíóúâêîûôåãõàèìòùřšěčůńýśćłęążźĄŃÝŚĆŁĘÄËÏÖÜÁÉÍÓÚÂÊÎÛÔÅÃÕÀÈÌÒÙŘŠĚČŮŻŹß ²ø®',
                     'aeiouaeiouaeiouaaoaeioursecunyscleazzANYSCLEAEIOUAEIOUAEIOUAAOAEIOURSECUZzs-2dR'
                     -- missing chars will be removed
                   )),
                   -- strip all others chars than [^a-z0-9 \-]
                   '[^a-z0-9 \-]',
                   '',
                   'g'
                );
            END;
            $$ LANGUAGE plpgsql;
SQL
        );
        $this->execute(<<<SQL
            CREATE OR REPLACE FUNCTION "person".person_slug()
            RETURNS trigger AS $$
            BEGIN
                IF NEW.slug_canonical IS NULL or NEW.slug_canonical='' THEN
                    NEW.slug_canonical = public.slugify(NEW.firstname || ' ' || NEW.lastname);
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
SQL
        );

        $this->execute(<<<SQL
            CREATE TRIGGER "person_slug"
              BEFORE INSERT OR UPDATE
              ON "person"."person"
              FOR EACH ROW
              EXECUTE PROCEDURE "person".person_slug();
SQL
        );
    }

    public function down()
    {
        $this->execute('DROP TABLE "person"."person_church";');
        $this->execute('DROP TABLE "person"."account";');
        $this->execute('DROP TABLE "person"."person";');
        $this->execute('DROP FUNCTION "person".person_slug();');
        $this->execute('DROP SCHEMA "person";');
    }
}
