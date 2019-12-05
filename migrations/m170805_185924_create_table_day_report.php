<?php

use yii\db\Migration;

class m170805_185924_create_table_day_report extends Migration
{
    public function up()
    {
        $this->createTable('day_report_trip_transport', [
            'id' => $this->primaryKey(),

            'date' => $this->integer()->comment('дата'),
            'direction_id' => $this->integer()->comment('Направление'),
            'direction_name' => $this->string(20)->comment('Направление - краткое название'),

            'trip_id' => $this->integer()->comment('Рейс'),
            'trip_name' => $this->string(50)->comment('Рейс - название'),
            'trip_date_sended' => $this->integer()->comment('Дата/время отправки рейса'),
            'trip_sender_id' => $this->integer()->comment('Пользователь отправивший рейс'),
            'trip_sender_fio' => $this->string(50)->comment('ФИО пользователя отправившего рейс'),

            'trip_transport_id' => $this->integer()->comment('Траспорт на рейсе'),

            'transport_id' => $this->integer()->comment('Траспорт'),
            'transport_car_reg' => $this->string(20)->comment('Гос. номер т/с'),
            'transport_model' => $this->string(50)->comment('Марка т/с'),
            'transport_places_count' => $this->smallInteger(2)->comment('Количество мест т/с'),
            'transport_date_sended' => $this->integer()->comment('Дата/время отправки т/с'),
            'transport_sender_id' => $this->integer()->comment('Отправитель т/с'),
            'transport_sender_fio' => $this->string(50)->comment('ФИО отправителя т/с'),

            'driver_id' => $this->integer()->comment('Водитель'),
            'driver_fio' => $this->string(100)->comment('Водитель - ФИО'),

            'places_count_sent' => $this->smallInteger()->comment('Количество мест всего отправлено'),
            'child_count_sent' => $this->smallInteger()->comment('Детских мест отправлено'),
            'student_count_sent' => $this->smallInteger()->comment('Студенческих мест отправлено'),
            'bag_count_sent' => $this->smallInteger()->comment('Количество сумок отправлено'),
            'suitcase_count_sent' => $this->smallInteger()->comment('Количество чемоданов отправлено'),
            'oversized_count_sent' => $this->smallInteger()->comment('Количество негабаритов отправлено'),
            'is_not_places_count_sent' => $this->smallInteger()->comment('Количество посылок отправлено (количество "безмест") '),
            'proceeds' => $this->decimal(8,2)->defaultValue(0)->comment('Общая выручка'),
        ]);
    }

    public function down()
    {
        $this->dropTable('day_report_trip_transport');
    }
}
