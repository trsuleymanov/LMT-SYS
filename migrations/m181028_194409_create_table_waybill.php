<?php

use yii\db\Migration;

/**
 * Class m181028_194409_create_table_waybill
 */
class m181028_194409_create_table_waybill extends Migration
{
    public function up()
    {
//        +1) номер в виде строки;
//        +2) дата выдачи;
//        +3) т/с, на которое выдан лист (<-);
//        +4) водитель, на которого выдан лист (<-);
//
//        +5) отметка о прохождении предрейсового медосмотра;
//        +6) время прохождения;
//        +7) отметка о предрейсовом техосмотре;
//        +8) время прохождения техосмотра;
//
//        +9-12) то же самое, только для послерейсовых осмотров;
//
//        +13) показания пробега при выезде;
//        +14) при возврате; (наверно показания пробега)
//        +15) время выезда;
//        +16) время возврата
//        + 17 и 18) рейсы круга (<-);

//        19) время создания;
//        20) создатель;
//        21) история изменений в текстовом формате типа (кто, во сколько)


        $this->createTable('transport_waybill', [
            'id' => $this->primaryKey(),
            'number' => $this->string(10)->comment('Номер'),
            'date_of_issue' => $this->integer()->comment('Дата выдачи'),
            'transport_id' => $this->integer()->comment('Траспорт'),
            'driver_id' => $this->integer()->comment('Водитель'),
            'pre_trip_med_check' => $this->boolean()->defaultValue(0)->comment('Мед. осмотр (предрейсовый)'),
            'pre_trip_med_check_time' => $this->integer()->comment('Дата/Время прохождения мед. осмотра (предрейсовый)'),
            'pre_trip_tech_check' => $this->boolean()->defaultValue(0)->comment('Тех. осмотр (предрейсовый)'),
            'pre_trip_tech_check_time' => $this->integer()->comment('Дата/Время прохождения тех. осмотра (предрейсовый)'),

            'after_trip_med_check' => $this->boolean()->defaultValue(0)->comment('Мед. осмотр (подрейсовый)'),
            'after_trip_med_check_time' => $this->integer()->comment('Дата/Время прохождения мед. осмотра (подрейсовый)'),
            'after_trip_tech_check' => $this->boolean()->defaultValue(0)->comment('Тех. осмотр (подрейсовый)'),
            'after_trip_tech_check_time' => $this->integer()->comment('Дата/Время прохождения тех. осмотра (подрейсовый)'),

            'mileage_before_departure' => $this->integer()->comment('Показания пробега при выезде'),
            'mileage_after_departure' => $this->integer()->comment('Показания пробега при возврате'),
            'departure_time' => $this->integer()->comment('Дата/Время выезда'),
            'return_time' => $this->integer()->comment('Дата/Время возврата'),
            'trip_transport_start' => $this->integer()->comment('Рейс стартовый'),
            'trip_transport_end' => $this->integer()->comment('Рейс обратный'),

            'created_at' => $this->integer()->comment('Дата создания'),
            'creator_id' => $this->integer()->comment('Создатель'),
            'changes_history' => $this->string(255)->comment('История изменений')
        ]);
    }

    public function down()
    {
        $this->dropTable('transport_waybill');
    }
}
