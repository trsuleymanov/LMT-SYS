<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detail_measurement_value".
 *
 * @property int $id
 * @property string $name Название
 */
class DetailMeasurementValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detail_measurement_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 30],
            [['count_is_double'], 'boolean'],
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
            'count_is_double' => 'Единицы измерения имеют дробную часть'
        ];
    }
}
