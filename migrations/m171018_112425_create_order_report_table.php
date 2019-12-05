<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_report`.
 */
class m171018_112425_create_order_report_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order_report', [
            'id' => $this->primaryKey(),
            'date_sended' => $this->integer()->comment('Дата отправки'),
            'order_id' => $this->integer()->comment('id заказа'),
            'client_id' => $this->integer()->comment('Клиент'),
            'client_name' => $this->string()->comment('Клиента имя'),
            'date' => $this->integer()->comment('дата'),
            'direction_id' => $this->integer()->comment('Направление'),
            'direction_name' => $this->string(20)->comment('Направление - название'),
            'street_id_from' => $this->integer()->comment('Улица откуда'),
            'street_from_name' => $this->string(50)->comment('Улица откуда - название'),
            'point_id_from' => $this->integer()->comment('Точка откуда'),
            'point_from_name' => $this->string(50)->comment('Точка откуда - название'),
            'time_air_train_arrival' => $this->string(5)->comment('Время прибытия поезда / посадки самолета'),
            'street_id_to' => $this->integer()->comment('Улица куда'),
            'street_to_name' => $this->string(50)->comment('Улица куда - название'),
            'point_id_to' => $this->integer()->comment('Точка куда'),
            'point_to_name' => $this->string(50)->comment('Точка куда - название'),
            'time_air_train_departure' => $this->string(5)->comment('Время отправления поезда / начало регистрации авиарейса'),
            'trip_id' => $this->integer()->comment('Рейс'),
            'trip_name' => $this->string(50)->comment('Рейс - название'),
            'informer_office_id' => $this->integer()->comment('Информаторская'),
            'informer_office_name' =>  $this->string()->comment('Информаторская - название'),
            'is_not_places' => $this->smallInteger(1)->defaultValue(0)->comment('Без места (отправляется посылка)'),
            'places_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество мест всего'),
            'student_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество мест для студентов'),
            'child_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество детских мест'),
            'bag_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество сумок'),
            'suitcase_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество чемоданов'),
            'oversized_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество негабаритов'),
            'prize_trip_count' => $this->smallInteger(2)->defaultValue(0)->comment('Количество призовых поездок'),
            'comment' => $this->string()->comment('Пожелания'),
            'additional_phone_1' => $this->string(20)->comment('Дополнительный телефон 1'),
            'additional_phone_2' => $this->string(20)->comment('Дополнительный телефон 2'),
            'additional_phone_3' => $this->string(20)->comment('Дополнительный телефон 3'),
            'time_sat' => $this->integer()->comment('Время посадки в машину'),
            'use_fix_price' => $this->smallInteger(1)->defaultValue(0)->comment('Используется фиксированная цена'),
            'price' => $this->decimal(8, 2)->defaultValue(0)->comment('Цена'),
            'time_confirm' => $this->integer()->comment('ВРПТ (Время подтверждения)'),
            'time_vpz' => $this->integer()->comment('ВПЗ - Время первичной записи - редактируемое время которое определяет приоритет внимания к заказу'),
            'is_confirmed' => $this->smallInteger(1)->defaultValue(0)->comment('Подтвержден'),
            'first_writedown_click_time' => $this->integer()->comment('Время первичного нажатия кнопки Записать'),
            'first_writedown_clicker_id' => $this->integer()->comment('Пользователь (диспетчер) впервые нажавший кнопку Записать'),
            'first_writedown_clicker_name' => $this->string(50)->comment('Имя пользователя (диспетчера) впервые нажавшего кнопку Записать'),
            'first_confirm_click_time' => $this->integer()->comment('Время первичного нажатия кнопки Подтвердить'),
            'first_confirm_clicker_id' => $this->integer()->comment('Пользователь (диспетчер) впервые нажавший кнопку Подтвердить'),
            'first_confirm_clicker_name' => $this->string(50)->comment('Имя пользователя (диспетчера) впервые нажавшего кнопку Подтвердить'),
            'radio_confirm_now' => $this->smallInteger(6)->comment('Группа radio-кнопок "Подтвердить сейчас" / "Не подтверждать"'),
            'radio_group_1' => $this->smallInteger(6)->comment('Первая группа radio-кнопок'),
            'radio_group_2' => $this->smallInteger(6)->comment('Вторая группа radio-кнопок'),
            'radio_group_3' => $this->smallInteger(6)->comment('Третья группа radio-кнопок'),
            'confirm_selected_transport' => $this->smallInteger(1)->defaultValue(0)->comment('Клиент согласился с посадкой в выбранное т/с'),
            'fact_trip_transport_id' => $this->integer()->comment('Пассажиры заказа планируемо фактически посажены в транспорто-рейс trip_transport_id'),
            'fact_trip_transport_car_reg' => $this->string(20)->comment('Гос. номер т/с'),
            'fact_trip_transport_color' => $this->string(50)->comment('Цвет т/с'),
            'fact_trip_transport_model' => $this->string(50)->comment('Марка т/с'),
            'fact_trip_transport_driver_id' => $this->integer()->comment('Водитель т/с'),
            'fact_trip_transport_driver_fio' => $this->string(100)->comment('ФИО водителя т/с'),
            'has_penalty' => $this->smallInteger(1)->defaultValue(0)->comment('Наличие штрафа'),
            'relation_order_id' => $this->integer()->comment('Связанный заказ'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order_report');
    }
}
