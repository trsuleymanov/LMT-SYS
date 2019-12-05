<?php

use yii\db\Migration;

class m170705_094430_add_field_date_sended_to_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('trip_transport', 'date_sended', $this->integer()->comment('Дата/время отправки машины'));
    }

    public function down()
    {
        $this->dropColumn('trip_transport', 'date_sended');
    }
}
