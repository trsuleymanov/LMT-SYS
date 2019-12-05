<?php

use yii\db\Migration;

/**
 * Handles adding date to table `second_trip_transport`.
 */
class m170730_130431_add_date_column_to_second_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('second_trip_transport', 'date', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('second_trip_transport', 'date');
    }
}
