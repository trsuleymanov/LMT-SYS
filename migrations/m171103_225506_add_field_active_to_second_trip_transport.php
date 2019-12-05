<?php

use yii\db\Migration;

class m171103_225506_add_field_active_to_second_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('second_trip_transport', 'active', $this->boolean()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('second_trip_transport', 'active');
    }
}
