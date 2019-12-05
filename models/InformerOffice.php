<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "informer_office".
 *
 * @property integer $id
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 */
class InformerOffice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informer_office';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['created_at', 'updated_at', 'do_tariff_id'], 'integer'],
            [['cashless_payment'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['code', 'name'], 'unique'],
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
            'code' => 'Код источника (нужен для работы ПО)',
            'cashless_payment' => 'Безналичная оплата',
            'do_tariff_id' => 'Признак формирования цены',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }

    public function getDoTariff()
    {
        return $this->hasOne(DoTariff::className(), ['id' => 'do_tariff_id']);
    }
}
