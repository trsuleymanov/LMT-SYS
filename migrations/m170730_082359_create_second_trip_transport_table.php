<?php

use yii\db\Migration;

/**
 * Handles the creation of table `second_trip_transport`.
 */
class m170730_082359_create_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('second_trip_transport', [
            'id' => $this->primaryKey(),
            'transport_id' => $this->integer(),
            'driver_id' => $this->integer(),
	    'second_driver_id' => $this->integer()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('second_trip_transport');
    }
}
