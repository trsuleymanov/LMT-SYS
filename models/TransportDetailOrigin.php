<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_detail_origin".
 *
 * @property int $id
 * @property string $name Происхождение
 */
class TransportDetailOrigin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport_detail_origin';
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
            'name' => 'Происхождение',
        ];
    }
}
