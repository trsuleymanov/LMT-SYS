<?php

use yii\db\Migration;

/**
 * Handles adding confirmed_date_time to table `trip_transport`.
 */
class m170712_120208_add_confirmed_date_time_column_to_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('trip_transport', 'confirmed_date_time', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('trip_transport', 'confirmed_date_time');
    }
}
