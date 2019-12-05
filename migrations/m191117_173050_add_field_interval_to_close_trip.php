<?php

use yii\db\Migration;

/**
 * Class m191117_173050_add_field_interval_to_close_trip
 */
class m191117_173050_add_field_interval_to_close_trip extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'interval_to_close_trip', $this->integer()->after('count_hours_before_trip_to_cancel_order')->defaultValue(120)->comment('Количество минут после последней точки рейса для закрытия рейса'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'interval_to_close_trip');
    }
}
