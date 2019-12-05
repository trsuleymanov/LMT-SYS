<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_model".
 *
 * @property int $id
 * @property string $name Наименование
 * @property string $sh_name Сокращенное наименование
 */
class TransportModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport_model';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50],
            [['sh_name'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'sh_name' => 'Сокращенное наименование',
        ];
    }
}
