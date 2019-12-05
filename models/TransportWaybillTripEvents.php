<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_waybill_trip_events".
 *
 * @property int $id
 * @property string $name Наименование
 */
class TransportWaybillTripEvents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transport_waybill_trip_events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }
}
