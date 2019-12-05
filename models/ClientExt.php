<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client_ext".
 *
 * @property integer $id
 * @property integer $client_server_ext_id
 * @property integer $status
 * @property integer $direction_id
 * @property integer $data_mktime
 * @property string $time
 * @property string $client_fio
 * @property integer $created_at
 * @property integer $updated_at
 */
class ClientExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_ext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_server_ext_id', 'direction_id', 'data_mktime',
                'start_processing_operator_id', 'start_processing_time',
                'created_at', 'updated_at', 'yandex_point_from_id', 'yandex_point_to_id'], 'integer'],
            [['time'], 'string', 'max' => 5],
            [['client_fio'], 'string', 'max' => 100],
            [['status', 'client_phone'], 'string', 'max' => 20],
            [['client_email'], 'string', 'max' => 50],
            [['yandex_point_from_name', 'yandex_point_to_name', ], 'string', 'max' => 255],
            [['yandex_point_from_lat', 'yandex_point_from_long', 'yandex_point_to_lat', 'yandex_point_to_long',
                'price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_server_ext_id' => 'id заявки на клиентском сервере',
            'status' => 'Статус',
            'direction_id' => 'Направление',
            'data_mktime' => 'Дата',
            'time' => 'Время',
            'places_count' => 'Количество мест',
            'price' => 'Цена',
            'yandex_point_from_id' => 'id яндекс-точки откуда',
            'yandex_point_from_name' => 'Яндекс-точка откуда',
            'yandex_point_from_lat' => 'Широта яндекс-точки откуда',
            'yandex_point_from_long' => 'Долгота яндекс-точки откуда',
            'yandex_point_to_id' => 'id яндекс-точки куда',
            'yandex_point_to_name' => 'Яндекс-точка куда',
            'yandex_point_to_lat' => 'Широта яндекс-точки куда',
            'yandex_point_to_long' => 'Долгота яндекс-точки куда',

            'client_fio' => 'Фамилия клиента',
            'client_phone' => 'Телефон клиента',
            'client_email' => 'Эл.почта клиента',
            'start_processing_operator_id' => 'Оператор первым начавшим обрабатывать заявку',
            'start_processing_time' => 'Время первого нажатия на кнопку "Обработать" заявку',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }


    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    public function getStartProcessingOperator()
    {
        return $this->hasOne(User::className(), ['id' => 'start_processing_operator_id']);
    }

    public function searchClient() {
        //    На основном сервере когда на основе заявки создается заказ, то проверяется:
        //  - если client_ext.client_email уже есть среди клиентов, то объект client находиться.
        //        - если client_ext.client_email не найден среди клиентов, то ищеться клиент с таким же телефоном.
        //        - если телефон найден, то объект client находиться и в этот объект записывается client_ext.client_email
        //        - это не тут реализуется: если телефон не найден, то создается новый объект client. И он дальше используется в заказе.

        $client = Client::find()->where(['email' => $this->client_email])->one();
        if($client == null) {
            $client = Client::find()->where(['mobile_phone' => $this->client_phone])->one();
        }

        return $client;
    }
}
