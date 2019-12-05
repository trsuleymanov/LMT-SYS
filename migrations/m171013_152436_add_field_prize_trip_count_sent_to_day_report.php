<?php

use yii\db\Migration;

class m171013_152436_add_field_prize_trip_count_sent_to_day_report extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_trip_transport', 'prize_trip_count_sent', $this->smallInteger()->defaultValue(0)->comment('Количество призовых поездок')->after('student_count_sent'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'prize_trip_count_sent');
    }
}
