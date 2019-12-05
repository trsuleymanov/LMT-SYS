<?php

namespace app\models;

use Yii;
use app\models\Order;
use app\models\Client;
use app\models\Direction;
use app\models\Street;
use app\models\Point;
use app\models\Trip;
use app\models\InformerOffice;
use app\models\TripTransport;
use app\models\DayReportTripTransport;

/**
 * This is the model class for table "order_report".
 *
 * @property integer $id
 * @property integer $day_report_trip_transport_id
 * @property integer $date_sended
 * @property integer $order_id
 * @property integer $client_id
 * @property string $client_name
 * @property integer $date
 * @property integer $direction_id
 * @property string $direction_name
 * @property integer $street_id_from
 * @property string $street_from_name
 * @property integer $point_id_from
 * @property string $point_from_name
 * @property string $time_air_train_arrival
 * @property integer $street_id_to
 * @property string $street_to_name
 * @property integer $point_id_to
 * @property string $point_to_name
 * @property string $time_air_train_departure
 * @property integer $trip_id
 * @property string $trip_name
 * @property integer $informer_office_id
 * @property string $informer_office_name
 * @property integer $is_not_places
 * @property integer $places_count
 * @property integer $student_count
 * @property integer $child_count
 * @property integer $bag_count
 * @property integer $suitcase_count
 * @property integer $oversized_count
 * @property integer $prize_trip_count
 * @property string $comment
 * @property string $additional_phone_1
 * @property string $additional_phone_2
 * @property string $additional_phone_3
 * @property integer $time_sat
 * @property integer $use_fix_price
 * @property string $price
 * @property integer $time_confirm
 * @property integer $is_confirmed
 * @property integer $first_writedown_click_time
 * @property integer $first_writedown_clicker_id
 * @property string $first_writedown_clicker_name
 * @property integer $first_confirm_click_time
 * @property integer $first_confirm_clicker_id
 * @property string $first_confirm_clicker_name
 * @property integer $radio_confirm_now
 * @property integer $radio_group_1
 * @property integer $radio_group_2
 * @property integer $radio_group_3
 * @property integer $confirm_selected_transport
 * @property integer $fact_trip_transport_id
 * @property string $fact_trip_transport_car_reg
 * @property string $fact_trip_transport_color
 * @property string $fact_trip_transport_model
 * @property integer $fact_trip_transport_driver_id
 * @property string $fact_trip_transport_driver_fio
 * @property integer $has_penalty
 * @property integer $relation_order_id
 */
class OrderReport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_sended', 'order_id', 'client_id', 'date', 'direction_id', 'street_id_from',
                'point_id_from', 'street_id_to', 'point_id_to', 'trip_id', 'informer_office_id',
                'is_not_places', 'places_count', 'student_count', 'child_count', 'bag_count',
                'suitcase_count', 'oversized_count', 'prize_trip_count', 'time_sat', 'use_fix_price',
                'time_confirm',
                // 'time_vpz',
                'is_confirmed', 'first_writedown_click_time',
                'first_writedown_clicker_id', 'first_confirm_click_time', 'first_confirm_clicker_id',
                'radio_confirm_now', 'radio_group_1', 'radio_group_2', 'radio_group_3',
                'confirm_selected_transport', 'fact_trip_transport_id', 'fact_trip_transport_driver_id',
                'has_penalty', 'relation_order_id', 'day_report_trip_transport_id'], 'integer'],
            [['price'], 'number'],
            [['client_name', 'informer_office_name', 'comment'], 'string', 'max' => 255],
            [['direction_name', 'additional_phone_1', 'additional_phone_2', 'additional_phone_3', 'fact_trip_transport_car_reg'], 'string', 'max' => 20],
            [['street_from_name', 'point_from_name', 'street_to_name', 'point_to_name', 'trip_name', 'first_writedown_clicker_name', 'first_confirm_clicker_name', 'fact_trip_transport_color', 'fact_trip_transport_model'], 'string', 'max' => 50],
            [['time_air_train_arrival', 'time_air_train_departure'], 'string', 'max' => 5],
            [['fact_trip_transport_driver_fio'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'day_report_trip_transport_id' => 'Отчет дня для отправленного транспорта',
            'date_sended' => 'Дата отправки',
            'order_id' => 'id заказа',
            'client_id' => 'Клиент',
            'client_name' => 'Клиента имя',
            'date' => 'дата',
            'direction_id' => 'Направление',
            'direction_name' => 'Направление - название',
            'street_id_from' => 'Улица откуда',
            'street_from_name' => 'Улица откуда - название',
            'point_id_from' => 'Точка откуда',
            'point_from_name' => 'Точка откуда - название',
            'time_air_train_arrival' => 'Время прибытия поезда / посадки самолета',
            'street_id_to' => 'Улица куда',
            'street_to_name' => 'Улица куда - название',
            'point_id_to' => 'Точка куда',
            'point_to_name' => 'Точка куда - название',
            'time_air_train_departure' => 'Время отправления поезда / начало регистрации авиарейса',
            'trip_id' => 'Рейс',
            'trip_name' => 'Рейс - название',
            'informer_office_id' => 'Источник',
            'informer_office_name' => 'Источник - название',
            'is_not_places' => 'Без места (отправляется посылка)',
            'places_count' => 'Количество мест всего',
            'student_count' => 'Количество мест для студентов',
            'child_count' => 'Количество детских мест',
            'bag_count' => 'Количество сумок',
            'suitcase_count' => 'Количество чемоданов',
            'oversized_count' => 'Количество негабаритов',
            'prize_trip_count' => 'Количество призовых поездок',
            'comment' => 'Пожелания',
            'additional_phone_1' => 'Дополнительный телефон 1',
            'additional_phone_2' => 'Дополнительный телефон 2',
            'additional_phone_3' => 'Дополнительный телефон 3',
            'time_sat' => 'Время посадки в машину',
            'use_fix_price' => 'Используется фиксированная цена',
            'price' => 'Цена',
            'time_confirm' => 'ВРПТ (Время подтверждения)',
            // 'time_vpz' => 'ВПЗ - Время первичной записи - редактируемое время которое определяет приоритет внимания к заказу',
            'is_confirmed' => 'Подтвержден',
            'first_writedown_click_time' => 'Время первичного нажатия кнопки Записать',
            'first_writedown_clicker_id' => 'Пользователь (диспетчер) впервые нажавший кнопку Записать',
            'first_writedown_clicker_name' => 'Имя пользователя (диспетчера) впервые нажавшего кнопку Записать',
            'first_confirm_click_time' => 'Время первичного нажатия кнопки Подтвердить',
            'first_confirm_clicker_id' => 'Пользователь (диспетчер) впервые нажавший кнопку Подтвердить',
            'first_confirm_clicker_name' => 'Имя пользователя (диспетчера) впервые нажавшего кнопку Подтвердить',
            'radio_confirm_now' => 'Группа radio-кнопок \"Подтвердить сейчас\" / \"Не подтверждать\"',
            'radio_group_1' => 'Первая группа radio-кнопок',
            'radio_group_2' => 'Вторая группа radio-кнопок',
            'radio_group_3' => 'Третья группа radio-кнопок',
            'confirm_selected_transport' => 'Клиент согласился с посадкой в выбранное т/с',
            'fact_trip_transport_id' => 'Пассажиры заказа планируемо фактически посажены в транспорто-рейс trip_transport_id',
            'fact_trip_transport_car_reg' => 'Гос. номер т/с',
            'fact_trip_transport_color' => 'Цвет т/с',
            'fact_trip_transport_model' => 'Марка т/с',
            'fact_trip_transport_driver_id' => 'Водитель т/с',
            'fact_trip_transport_driver_fio' => 'ФИО водителя т/с',
            'has_penalty' => 'Наличие штрафа',
            'relation_order_id' => 'Связанный заказ',
        ];
    }


    public function getDayReportTripTransport()
    {
        return $this->hasOne(DayReportTripTransport::className(), ['id' => 'day_report_trip_transport_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getStreetFrom()
    {
        return $this->hasOne(Street::className(), ['id' => 'street_id_from']);
    }

    public function getStreetTo()
    {
        return $this->hasOne(Street::className(), ['id' => 'street_id_to']);
    }

    public function getPointFrom()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_from']);
    }

    public function getPointTo()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_to']);
    }

    public function getTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'trip_id']);
    }

    public function getInformerOffice()
    {
        return $this->hasOne(InformerOffice::className(), ['id' => 'informer_office_id']);
    }

    public function getFactTripTransport()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'fact_trip_transport_id']);
    }

    public function getRelationOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'relation_order_id']);
    }
}
