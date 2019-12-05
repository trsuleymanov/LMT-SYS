<?php

use yii\db\Migration;

class m171229_000809_create_table_transport_circle extends Migration
{
    public function up()
    {
        $this->createTable('day_report_transport_circle', [
            'id' => $this->primaryKey(),
            'transport_id' => $this->integer()->comment('Транспорт'),
            'base_city_trip_id' => $this->integer()->comment('Рейс выезда из города базирования'),
            'base_city_trip_start_time' => $this->integer()->comment('Время первой точки рейса города базирования'),
            'base_city_day_report_id' => $this->integer()->comment('id отчета дня отправки из города базирования'),
            'notbase_city_trip_id' => $this->integer()->comment('Рейс выезда из промежуточного города'),
            'notbase_city_trip_start_time' => $this->integer()->comment('Время первой точки рейса промежуточного города'),
            'notbase_city_day_report_id' => $this->integer()->comment('id отчета дня отправки из промежуточного города'),
            'state' => $this->smallInteger(1)->comment('Состояние круга'),
            'total_proceeds' => $this->decimal(8,2)->defaultValue(0)->comment('Общая выручка'),
        ]);
    }

    public function down()
    {
        $this->dropTable('day_report_transport_circle');
    }
}
