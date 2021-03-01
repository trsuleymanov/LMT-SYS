<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%transport_waybill_type}}".
 *
 * @property int $id
 * @property string $name Название
 * @property int $allow_minus_opearation Разрешены отрицательные суммы денег
 */
class TransportWaybillType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%transport_waybill_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['allow_minus_opearation'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'allow_minus_opearation' => 'Разрешены отрицательные суммы денег',
        ];
    }
}
