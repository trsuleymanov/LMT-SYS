<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "working_shift".
 *
 * @property int $id
 * @property int $user_id Пользователь
 * @property string $shift_type
 * @property int $start_time Время начала смены
 * @property int $finish_time Время завершения смены
 */
class WorkingShift extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'working_shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'start_time', 'finish_time'], 'integer'],
            [['shift_type'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'shift_type' => 'Тип смены',
            'start_time' => 'Время начала смены',
            'finish_time' => 'Время завершения смены',
        ];
    }

    public static function getShiftTypes() {
        return [
            'ka_day' => 'КА день',
            'ak_day' => 'АК день',
            'night' => 'Ночная смена',
            'tube' => 'Трубки'
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
