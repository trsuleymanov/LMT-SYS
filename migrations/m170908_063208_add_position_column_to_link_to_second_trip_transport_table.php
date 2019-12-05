<?php

use yii\db\Migration;

/**
 * Handles adding position to table `link_to_second_trip_transport`.
 */
class m170908_063208_add_position_column_to_link_to_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('link_to_second_trip_transport', 'trip_transport2_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('link_to_second_trip_transport', 'trip_transport2_id');
    }
}
