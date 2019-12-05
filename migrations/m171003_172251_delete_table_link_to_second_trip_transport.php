<?php

use yii\db\Migration;

class m171003_172251_delete_table_link_to_second_trip_transport extends Migration
{
    public function up()
    {
        $this->dropTable('link_to_second_trip_transport');
    }

    public function down()
    {
        $this->createTable('link_to_second_trip_transport', [
            'id' => $this->primaryKey(),
            'trip_transport_id' => $this->integer(),
            'second_trip_transport_id' => $this->integer(),
            'trip_transport1_id' => $this->integer(),
            'trip_transport2_id' => $this->integer(),
        ]);
    }
}
