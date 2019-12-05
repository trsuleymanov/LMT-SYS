<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notaccountability_transport_report".
 *
 * @property int $id
 * @property int $date_of_issue Дата выдачи
 * @property int $transport_id Траспорт
 * @property int $driver_id Водитель
 * @property int $trip_transport_start Рейс стартовый
 * @property int $trip_transport_end Рейс обратный
 * @property string $hand_over_b1 сдано B1
 * @property int $hand_over_b1_data Дата (когда сдано B1)
 * @property int $set_hand_over_b1_operator_id Оператор установивший сумму оплату b1
 * @property int $set_hand_over_b1_time Время установки суммы оплаты b1
 * @property string $hand_over_b2 сдано B2
 * @property int $hand_over_b2_data Дата (когда сдано B2)
 * @property int $set_hand_over_b2_operator_id Оператор установивший сумму оплату b2
 * @property int $set_hand_over_b2_time Время установки суммы оплаты b2
 */
class NotaccountabilityTransportReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notaccountability_transport_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start_circle', 'date_end_circle', 'transport_id', 'driver_id', 'trip_transport_start',
                'trip_transport_end', 'set_hand_over_operator_id', 'set_hand_over_time', 'day_report_transport_circle_id'], 'integer'],
            [['hand_over', 'formula_percent'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transport_id' => 'Траспорт',
            'driver_id' => 'Водитель',

            'date_start_circle' => 'Дата выезда на круг',
            'date_end_circle' => 'Дата выезда на завершение круга',

            'day_report_transport_circle_id' => 'Круг рейсов',
            'trip_transport_start' => 'Рейс стартовый',
            'trip_transport_end' => 'Рейс обратный',

            'hand_over' => 'Сдано',
            'set_hand_over_operator_id' => 'Оператор установивший сумму оплату',
            'set_hand_over_time' => 'Время установки суммы оплаты',
            'formula_percent' => '% рассчитанный по формуле',
        ];
    }


    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
    }

    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }

    public function getTripTransportStart()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'trip_transport_start']);
    }

    public function getTripTransportEnd()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'trip_transport_end']);
    }

    public function getHandOverOperator() {
        return $this->hasOne(User::className(), ['id' => 'set_hand_over_operator_id']);
    }

}
