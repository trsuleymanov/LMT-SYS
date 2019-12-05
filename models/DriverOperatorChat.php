<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "driver_operator_chat".
 *
 * @property int $id
 * @property int $trip_transport_id trip_transport_id
 * @property int $driver_id Водитель
 * @property int $operator_id Оператор
 * @property string $message_from_driver Сообщение водителя
 * @property int $message_from_driver_at Время прихода на сервер сообщения водителя
 * @property string $answer_from_operator Ответ оператора
 * @property int $answer_from_operator_at Время создания ответа оператора
 * @property int $driver_is_read_at Время когда водитель "прочитал" ответ диспетчера
 */
class DriverOperatorChat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'driver_operator_chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trip_transport_id', 'driver_id', 'operator_id', 'message_from_driver_at', 'answer_from_operator_at', 'driver_is_read_at'], 'integer'],
            [['message_from_driver', 'answer_from_operator'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trip_transport_id' => 'trip_transport_id',
            'driver_id' => 'Водитель',
            'operator_id' => 'Оператор',
            'message_from_driver' => 'Сообщение водителя',
            'message_from_driver_at' => 'Время прихода на сервер сообщения водителя',
            'answer_from_operator' => 'Ответ оператора',
            'answer_from_operator_at' => 'Время создания ответа оператора',
            'driver_is_read_at' => 'Время когда водитель \"прочитал\" ответ диспетчера',
        ];
    }


    public function getDriver()
    {
        return $this->hasOne(Driver::className(), ['id' => 'driver_id']);
    }


    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE '.self::tableName().' SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        return Yii::$app->db->createCommand($sql)->execute();
    }
}
