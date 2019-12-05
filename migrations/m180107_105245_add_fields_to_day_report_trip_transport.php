<?php

use yii\db\Migration;

class m180107_105245_add_fields_to_day_report_trip_transport extends Migration
{
    public function up()
    {
        $this->addColumn('day_report_trip_transport', 'airport_count_sent', $this->smallInteger()->defaultValue(0)->comment('Количество заказов с отправкой из/в аэропорт')->after('is_not_places_count_sent'));
        $this->addColumn('day_report_trip_transport', 'fix_price_count_sent', $this->smallInteger()->defaultValue(0)->comment('Количество заказов с фиксированной стоимостью')->after('airport_count_sent'));
    }

    public function down()
    {
        $this->dropColumn('day_report_trip_transport', 'airport_count_sent');
        $this->dropColumn('day_report_trip_transport', 'fix_price_count_sent');
    }
}
