<?php

use yii\db\Migration;

class m171013_124900_add_field_base_city_id_to_transport extends Migration
{
    public function up()
    {
        $this->addColumn('transport', 'base_city_id', $this->integer()->comment('Город базирования')->after('color'));
    }

    public function down()
    {
        $this->dropColumn('transport', 'base_city_id');
    }
}
