<?php

use yii\db\Migration;

/**
 * Class m200206_205914_add_fields_to_trip
 */
class m200206_205914_add_fields_to_trip extends Migration
{
    public function up()
    {
        $this->addColumn('trip', 'start_time_unixtime', $this->integer()->comment('Начало сбора')->after('end_time'));
        $this->addColumn('trip', 'mid_time_unixtime', $this->integer()->comment('Середина сбора')->after('start_time_unixtime'));
        $this->addColumn('trip', 'end_time_unixtime', $this->integer()->comment('Конец сбора')->after('mid_time_unixtime'));
    }

    public function down()
    {
        $this->dropColumn('trip', 'end_time_unixtime');
        $this->dropColumn('trip', 'mid_time_unixtime');
        $this->dropColumn('trip', 'start_time_unixtime');
    }
}
