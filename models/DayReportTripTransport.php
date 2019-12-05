<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\InformerOffice;
use app\models\Transport;

/**
 * This is the model class for table "day_report_trip_transport".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $direction_id
 * @property string $direction_name
 * @property integer $trip_id
 * @property string $trip_name
 * @property integer $trip_date_sended
 * @property integer $trip_sender_id
 * @property integer $trip_transport_id
 * @property integer $transport_id
 * @property string $transport_car_reg
 * @property string $transport_model
 * @property integer $transport_date_sended
 * @property integer $transport_sender_id
 * @property integer $driver_id
 * @property string $driver_fio
 * @property integer $places_count_sent
 * @property integer $child_count_sent
 * @property integer $student_count_sent
 * @property integer $prize_trip_count_sent
 * @property integer $bag_count_sent
 * @property integer $suitcase_count_sent
 * @property integer $oversized_count_sent
 * @property integer $is_not_places_count_sent
 * @property string $proceeds
 */
class DayReportTripTransport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'day_report_trip_transport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'direction_id', 'trip_id', 'trip_date_sended', 'trip_sender_id', 'trip_transport_id',
                'transport_id', 'transport_date_sended', 'transport_sender_id', 'driver_id',
                'places_count_sent', 'airport_places_count_sent', 'child_count_sent', 'student_count_sent',
                'prize_trip_count_sent', 'bag_count_sent',
                'suitcase_count_sent', 'oversized_count_sent', 'is_not_places_count_sent',
                'transport_places_count', 'transport_round_is_completed',
                'transport_round_completing_reason_id', 'airport_count_sent', 'fix_price_count_sent',
                'no_record'], 'integer'],
            [['proceeds', 'paid_summ'], 'number'],
            [['direction_name', 'transport_car_reg'], 'string', 'max' => 20],
            [['trip_name', 'transport_model', 'trip_sender_fio', 'transport_sender_fio'], 'string', 'max' => 50],
            [['driver_fio'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'дата',
            'direction_id' => 'Направление',
            'direction_name' => 'Направление - краткое название',
            'trip_id' => 'Рейс',
            'trip_name' => 'Рейс - название',
            'trip_date_sended' => 'Дата/время отправки рейса',
            'trip_sender_id' => 'Пользователь отправивший рейс',
            'trip_transport_id' => 'Траспорт на рейсе',
            'trip_sender_fio' => 'ФИО пользователя отправившего рейс',//
            'transport_id' => 'Траспорт',
            'transport_car_reg' => 'Гос. номер т/с',
            'transport_model' => 'Марка т/с',
            'transport_places_count' => 'Количество мест т/с',//
            'transport_date_sended' => 'Дата/время отправки т/с',
            'transport_sender_id' => 'Отправитель т/с',
            'transport_sender_fio' => 'ФИО отправителя т/с',//
            'transport_round_is_completed' => 'Круг завершен',
            'transport_round_completing_reason_id' => 'Причина завершения круга',
            'driver_id' => 'Водитель',
            'driver_fio' => 'Водитель - ФИО',
            'places_count_sent' => 'Количество мест всего отправлено',
            'child_count_sent' => 'Детских мест отправлено',
            'student_count_sent' => 'Студенческих мест отправлено',
            'prize_trip_count_sent' => 'Количество призовых поездок',
            'bag_count_sent' => 'Количество сумок отправлено',
            'suitcase_count_sent' => 'Количество чемоданов отправлено',
            'oversized_count_sent' => 'Количество негабаритов отправлено',
            'is_not_places_count_sent' => 'Количество посылок отправлено (количество \"безмест\") ',
            'no_record' => 'Без записи',
            'airport_count_sent' => 'Количество заказов с отправкой из/в аэропорт',
            'airport_places_count_sent' => 'Количество мест в заказах с отправкой из/в аэропорт',
            'fix_price_count_sent' => 'Количество заказов с фиксированной стоимостью',
            'proceeds' => 'Общая выручка',
            'paid_summ' => 'Оплачено',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransportSender()
    {
        return $this->hasOne(User::className(), ['id' => 'trip_sender_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
    }

    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }

    public function getTripTransport()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'trip_transport_id']);
    }
}
