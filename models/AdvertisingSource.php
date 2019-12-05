<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advertising_source".
 *
 * @property int $id
 * @property string $name Рекламный источник
 */
class AdvertisingSource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertising_source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Рекламный источник',
        ];
    }
}
