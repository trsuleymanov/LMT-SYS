<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "driver_photo".
 *
 * @property int $id
 * @property int $user_id Пользователь осуществивший выгрузку
 * @property int $time_loading_finish Время завершения выгрузки
 * @property int $photo_created_on_mobile Время создания скриншота на мобильном устройстве
 * @property string $photo_link Ссылка на скриншот
 */
class DriverPhoto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver_photo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'time_loading_finish', 'photo_created_on_mobile', 'transport_id', 'driver_id'], 'integer'],
            [['transport_car_reg'], 'string', 'max' => 20],
            [['photo_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь осуществивший выгрузку',
            'driver_id' => 'Водитель',
            'transport_id' => 'Машина',
            'transport_car_reg' => 'Номер машины',
            'time_loading_finish' => 'Время завершения выгрузки',
            'photo_created_on_mobile' => 'Время создания скриншота на мобильном устройстве',
            'photo_link' => 'Ссылка на скриншот',
        ];
    }

    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }

    public function getUserdriver()
    {
        return $this->hasOne(Driver::className(), ['user_id' => 'user_id']);
    }
}
