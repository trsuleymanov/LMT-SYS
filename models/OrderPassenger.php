<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "passenger".
 *
 * @property int $id
 * @property int $order_id Заказ
 * @property int $passenger_id Пассажир
 */
class OrderPassenger extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_passenger';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'passenger_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'passenger_id' => 'Пассажир',
        ];
    }


    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getPassenger()
    {
        return $this->hasOne(Passenger::className(), ['id' => 'passenger_id']);
    }
}
