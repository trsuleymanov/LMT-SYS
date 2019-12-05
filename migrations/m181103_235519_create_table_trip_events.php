<?php

use yii\db\Migration;

/**
 * Class m181103_235519_create_table_trip_events
 */
class m181103_235519_create_table_trip_events extends Migration
{
    public function up()
    {
        $this->createTable('transport_waybill_trip_events', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->comment('Наименование'),
        ]);


        $this->BatchInsert('transport_waybill_trip_events',
            ['name',],
            [
                ['Начало круга'],
                ['Начало второго круга в тот же день'],
                ['Завершение круга'],
                ['Завершение второго круга в тот же день'],
                ['Завершение второго круга в последующий день'],
                ['Доставка т/с на ремонт'],
                ['Забор т/с с ремонта'],
                ['Эвакуация на рейсе'],
                ['Эвакуация до или после окончания рейса'],
                ['ТО на рейсе'],
                ['Обнаружение поломки'],
                ['Плановый ремонт'],
                ['Внеплановый ремонт'],
                ['Простой в связи с ремонтом'],
                ['Простой в связи с отсутствием водителя'],
                ['Остановка сотрудником ГИБДД'],
                ['Остановка сотрудником ТИ'],
                ['Оформление протокола'],
                ['Предупреждение'],
                ['Конфликтная ситуация'],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('transport_waybill_trip_events');
    }
}
