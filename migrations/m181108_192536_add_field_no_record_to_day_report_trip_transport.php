<?php

use yii\db\Migration;

/**
 * Class m181108_192536_add_field_no_record_to_day_report_trip_transport
 */
class m181108_192536_add_field_no_record_to_day_report_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_trip_transport', 'no_record', $this->smallInteger()->comment('Без записи')->after('is_not_places_count_sent'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'no_record');
    }
}
