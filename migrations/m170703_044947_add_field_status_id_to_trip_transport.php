<?php

use yii\db\Migration;

class m170703_044947_add_field_status_id_to_trip_transport extends Migration
{
    public function safeUp()
    {
        $this->addColumn('trip_transport', 'status_id', $this->smallInteger()->defaultValue(0)->comment('Статус поездки'));
    }

    public function safeDown()
    {
        $this->dropColumn('trip_transport', 'status_id');
    }
}
