<?php

use yii\db\Migration;

/**
 * Class m180901_173838_add_indexes_to_tables
 */
class m180901_173838_add_indexes_to_tables extends Migration
{
    public function up()
    {
        $this->createIndex('status_id', 'order', 'status_id');
        $this->createIndex('fact_trip_transport_id', 'order', 'fact_trip_transport_id');
        $this->createIndex('trip_id', 'order', 'trip_id');
        $this->createIndex('order_temp_identifier', 'dispatcher_accounting', 'order_temp_identifier');
    }

    public function down()
    {
        $this->dropIndex('status_id', 'order');
        $this->dropIndex('fact_trip_transport_id', 'order');
        $this->dropIndex('trip_id', 'order');
        $this->dropIndex('order_temp_identifier', 'dispatcher_accounting');
    }

}
