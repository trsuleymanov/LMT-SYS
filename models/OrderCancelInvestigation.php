<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_cancel_investigation".
 *
 * @property int $id
 * @property int $order_id Заказ
 * @property int $trip_id Рейс
 * @property int $client_id Клиент
 * @property int $data Дата расследования
 * @property string $rejection_reason Реальная причина отказа
 * @property string $how_client_left На чем уехал клиент
 * @property string $complaints_and_wishes Жалобы и пожелания
 * @property int $updated_at Время сохранения
 */
class OrderCancelInvestigation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_cancel_investigation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'trip_id', 'client_id', 'updated_at'], 'integer'],
            [['rejection_reason', 'how_client_left', 'complaints_and_wishes'], 'string'],
            [['order_id', 'trip_id', 'client_id', 'data'], 'required'],

            [['data'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'trip_id' => 'Рейс',
            'client_id' => 'Клиент',
            'data' => 'Дата расследования',
            'rejection_reason' => 'Реальная причина отказа',
            'how_client_left' => 'На чем уехал клиент',
            'complaints_and_wishes' => 'Жалобы и пожелания',
            'updated_at' => 'Время сохранения',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'trip_id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
