<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_round_completing_reason".
 *
 * @property integer $id
 * @property string $name
 */
class TransportRoundCompletingReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport_round_completing_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Признак/причина завершения круга',
        ];
    }
}
