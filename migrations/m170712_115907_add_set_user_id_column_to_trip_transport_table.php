<?php

use yii\db\Migration;

/**
 * Handles adding set_user_id to table `trip_transport`.
 */
class m170712_115907_add_set_user_id_column_to_trip_transport_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('trip_transport', 'set_user_id', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('trip_transport', 'set_user_id');
    }
}
