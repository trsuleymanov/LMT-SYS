<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "working_shift_unlocking_time".
 *
 * @property int $id
 * @property int $working_shift_id Смена
 * @property int $created_at Время разблокировки
 */
class WorkingShiftUnlockingTime extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'working_shift_unlocking_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['working_shift_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'working_shift_id' => 'Смена',
            'created_at' => 'Время разблокировки',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }

        return parent::beforeSave($insert);
    }
}
