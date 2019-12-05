<?php

use yii\db\Migration;

/**
 * Class m181109_191754_change_fields_departure_time_return_time_in_transport_waybill
 */
class m181109_191754_change_fields_departure_time_return_time_in_transport_waybill extends Migration
{
    public function up()
    {
        $this->truncateTable('transport_waybill');

        $this->alterColumn('transport_waybill', 'departure_time', $this->string(10)->comment('Дата/Время выезда'));
        $this->alterColumn('transport_waybill', 'return_time', $this->string(10)->comment('Дата/Время возврата'));

        $this->alterColumn('transport_waybill', 'pre_trip_med_check_time', $this->string(10)->comment('Дата/Время прохождения мед. осмотра (предрейсовый)'));
        $this->alterColumn('transport_waybill', 'pre_trip_tech_check_time', $this->string(10)->comment('Дата/Время прохождения тех. осмотра (предрейсовый)'));
        $this->alterColumn('transport_waybill', 'after_trip_med_check_time', $this->string(10)->comment('Дата/Время прохождения мед. осмотра (подрейсовый)'));
        $this->alterColumn('transport_waybill', 'after_trip_tech_check_time', $this->string(10)->comment('Дата/Время прохождения тех. осмотра (подрейсовый)'));
    }

    public function down()
    {
        $this->truncateTable('transport_waybill');

        $this->alterColumn('transport_waybill', 'departure_time', $this->integer()->comment('Дата/Время выезда'));
        $this->alterColumn('transport_waybill', 'return_time', $this->integer()->comment('Дата/Время возврата'));

        $this->alterColumn('transport_waybill', 'pre_trip_med_check_time', $this->integer()->comment('Дата/Время прохождения мед. осмотра (предрейсовый)'));
        $this->alterColumn('transport_waybill', 'pre_trip_tech_check_time', $this->integer()->comment('Дата/Время прохождения тех. осмотра (предрейсовый)'));
        $this->alterColumn('transport_waybill', 'after_trip_med_check_time', $this->integer()->comment('Дата/Время прохождения мед. осмотра (подрейсовый)'));
        $this->alterColumn('transport_waybill', 'after_trip_tech_check_time', $this->integer()->comment('Дата/Время прохождения тех. осмотра (подрейсовый)'));
    }
}
