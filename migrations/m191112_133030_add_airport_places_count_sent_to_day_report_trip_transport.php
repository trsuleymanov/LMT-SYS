<?php

use yii\db\Migration;

/**
 * Class m191112_133030_add_airport_places_count_sent_to_day_report_trip_transport
 */
class m191112_133030_add_airport_places_count_sent_to_day_report_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_trip_transport', 'airport_places_count_sent', $this->smallInteger()->defaultValue(0)->comment('Количество мест в заказах с отправкой из/в аэропорт')->after('airport_count_sent'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'airport_places_count_sent');
    }
}
