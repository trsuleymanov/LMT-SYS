<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advertising_source_report".
 *
 * @property int $id
 * @property int $advertising_source_id Рекламный источник
 * @property int $client_id Клиент
 * @property int $created_at Время создания
 */
class AdvertisingSourceReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertising_source_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['advertising_source_id', 'phone'], 'required'],
            [['advertising_source_id', 'operator_user_id', 'client_id', 'created_at'], 'integer'],
            [['phone', ], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertising_source_id' => 'Рекламный источник',
            'operator_user_id' => 'Оператор',
            'client_id' => 'Клиент',
            'created_at' => 'Время создания',
            'phone' => 'Телефон',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();

            $this->operator_user_id = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

    public function getAdvertisingSource()
    {
        return $this->hasOne(AdvertisingSource::className(), ['id' => 'advertising_source_id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    public function getOperatorUser()
    {
        return $this->hasOne(User::className(), ['id' => 'operator_user_id']);
    }


    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }
        return Yii::$app->db->createCommand($sql)->execute();
    }
}
