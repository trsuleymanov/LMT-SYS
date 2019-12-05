<?php

use yii\db\Migration;

/**
 * Handles the creation of table `link_to_second_trip_transport`.
 */
class m170730_081055_create_link_to_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('link_to_second_trip_transport', [
            'id' => $this->primaryKey(),
            'trip_transport_id' => $this->integer(),
            'second_trip_transport_id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('link_to_second_trip_transport');
    }
}
