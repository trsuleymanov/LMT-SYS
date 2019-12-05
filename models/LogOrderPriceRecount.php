<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log_order_price_recount".
 *
 * @property int $id
 * @property int $trip_id Рейс
 * @property string $trip_link Ссылка на рейс для ленивых
 * @property int $order_id Заказ
 * @property string $old_price Старая цена
 * @property string $new_price Новая цена
 */
class LogOrderPriceRecount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_order_price_recount';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trip_id', 'order_id', 'created_at'], 'integer'],
            [['old_price', 'new_price'], 'number'],
            [['trip_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trip_id' => 'Рейс',
            'trip_link' => 'Ссылка на рейс для ленивых',
            'order_id' => 'Заказ',
            'old_price' => 'Старая цена',
            'new_price' => 'Новая цена',
            'created_at' => 'Время когда произошел пересчет цены',
        ];
    }
}
