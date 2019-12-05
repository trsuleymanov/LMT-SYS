<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "call_docking".
 *
 * @property int $id
 * @property int $call_id Звонок
 * @property int $case_id Обращение
 * @property int $conformity Да/Нет - определяет соответствие номера операнда и ID клиента
 * @property string $click_event Событие
 */
class CallDocking extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_docking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['call_id', 'case_id', 'conformity'], 'integer'],
            [['click_event'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'call_id' => 'Звонок',
            'case_id' => 'Обращение',
            'conformity' => 'Да/Нет - определяет соответствие номера операнда и ID клиента',
            'click_event' => 'Событие',
        ];
    }

    public function getCall()
    {
        return $this->hasOne(Call::className(), ['id' => 'call_id']);
    }
}
