<?php

use Phinx\Migration\AbstractMigration;

class Functions extends AbstractMigration
{
    public function up()
    {
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
    }

    public function down()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public".slugify();');
    }
}
