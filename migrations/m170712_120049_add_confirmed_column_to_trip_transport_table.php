<?php

use yii\db\Migration;

/**
 * Handles adding confirmed to table `trip_transport`.
 */
class m170712_120049_add_confirmed_column_to_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('trip_transport', 'confirmed', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('trip_transport', 'confirmed');
    }
}
