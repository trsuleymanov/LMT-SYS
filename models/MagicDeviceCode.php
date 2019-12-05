<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "magic_device_code".
 *
 * @property int $id
 * @property string $code Код устройства
 */
class MagicDeviceCode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'magic_device_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['code', 'string', 'min' => 15, 'max' => 17],
            [['code'], 'unique'],
            [['code'], 'checkCode'],
            [['description'], 'string'],
        ];
    }

    public function beforeValidate()
    {
        $this->code = trim($this->code);

        return parent::beforeValidate();
    }


    public function checkCode($attribute, $params)
    {
        $driver = Driver::find()->where(['device_code' => $this->code])->one();
        if($driver != null) {
            $this->addError($attribute, 'Текущий код используется у водителя '.$driver->fio);
        }else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Код устройства',
            'description' => 'Примечание'
        ];
    }
}
