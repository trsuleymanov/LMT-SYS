<?php

use yii\db\Migration;

/**
 * Handles adding position to table `second_trip_transport`.
 */
class m170908_062203_add_position_column_to_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('second_trip_transport', 'trip_transport1_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('second_trip_transport', 'trip_transport1_id');
    }
}
