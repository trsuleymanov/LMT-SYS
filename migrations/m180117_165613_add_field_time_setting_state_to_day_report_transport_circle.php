<?php

use yii\db\Migration;

class m180117_165613_add_field_time_setting_state_to_day_report_transport_circle extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_transport_circle', 'time_setting_state', $this->integer()->comment('Время установки статуса')->after('state'));
    }

    public function down()
    {
        $this->dropColumn('day_report_transport_circle', 'time_setting_state');
    }
}
