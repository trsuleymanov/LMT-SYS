<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_detail_state".
 *
 * @property int $id
 * @property string $name Состояние
 */
class TransportDetailState extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport_detail_state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Состояние',
        ];
    }
}
