<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_cancellation_reason".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 */
class OrderCancellationReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_cancellation_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'code' => 'Код',
        ];
    }
}
