<?php

use yii\db\Migration;

/**
 * Handles dropping position from table `link_to_second_trip_transport`.
 */
class m170908_063514_drop_position_column_from_link_to_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        //$this->dropColumn('link_to_second_trip_transport', 'trip_transport_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        //$this->addColumn('link_to_second_trip_transport', 'trip_transport_id', $this->integer());
    }
}
