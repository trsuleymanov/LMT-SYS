<?php

use yii\db\Migration;

/**
 * Class m191115_164806_upgrade_field_trip_operation_in_trip_operation
 */
class m191115_164806_upgrade_field_trip_operation_in_trip_operation extends Migration
{
    // 'type' => "ENUM('create', 'update', 'merge', 'set_commercial', 'unset_commercial', 'start_send', 'send', 'cancel_send')",

    public function up()
    {
        $this->alterColumn('trip_operation', 'type', "ENUM('create', 'update', 'merge', 'set_commercial', 'unset_commercial', 'start_send', 'issued_by_operator', 'send', 'cancel_send')");
    }

    public function down()
    {
        $this->alterColumn('trip_operation', 'type', "ENUM('create', 'update', 'merge', 'set_commercial', 'unset_commercial', 'start_send', 'send', 'cancel_send')");
    }
}
