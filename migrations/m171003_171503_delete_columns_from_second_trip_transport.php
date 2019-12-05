<?php

use yii\db\Migration;

class m171003_171503_delete_columns_from_second_trip_transport extends Migration
{
    public function up()
    {
        $this->dropColumn('second_trip_transport', 'driver_id');
        $this->dropColumn('second_trip_transport', 'second_driver_id');
        $this->dropColumn('second_trip_transport', 'trip_transport1_id');
        $this->dropColumn('second_trip_transport', 'trip_transport2_id');
    }

    public function down()
    {
        $this->addColumn('second_trip_transport', 'driver_id', $this->integer());
        $this->addColumn('second_trip_transport', 'second_driver_id', $this->integer());
        $this->addColumn('second_trip_transport', 'trip_transport1_id', $this->integer());
        $this->addColumn('second_trip_transport', 'trip_transport2_id', $this->integer());
    }
}
