<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cashback_setting".
 *
 * @property int $id
 * @property int $start_date Дата начала использования
 * @property int $order_accrual_percent Процент начисления за заказ
 * @property int $order_penalty_percent Процент штафа с заказа
 * @property int $hours_before_start_trip_for_penalty Часы до начала рейса являющиеся условием начисления штрафа
 * @property int $with_commercial_trips Да/Нет - накапливать ли кэш-бэк во время коммерческих рейсов
 */
class CashbackSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashback_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_accrual_percent', 'order_penalty_percent', 'hours_before_start_trip_for_penalty',
                'with_commercial_trips', 'has_cashback_for_prepayment', 'has_cashback_for_nonprepayment'], 'integer'],
            [['start_date'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Дата запуска',
            'has_cashback_for_prepayment' => 'Кэш-бэк предоплаты',
            'has_cashback_for_nonprepayment' => 'Обычный кэш-бэк',
            'order_accrual_percent' => 'Процент начисления за заказ (от 0 до 100)',
            'order_penalty_percent' => 'Процент штрафа с заказа (от 0 до 100)',
            'hours_before_start_trip_for_penalty' => 'Количество часов до начала рейса являющиеся условием начисления штрафа',
            'with_commercial_trips' => 'Накапливать кэш-бэк во время коммерческих рейсов',
        ];
    }

    public function beforeSave($insert)
    {
        if(isset($this->start_date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->start_date)) {
            $this->start_date = strtotime($this->start_date);   // convent '07.11.2016' to unixtime
        }

        return parent::beforeSave($insert);
    }
}
