<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_status".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 */

class Status {
    public $id;
    public $name;
    public $code;
}

class OrderStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['name', 'code'], 'string', 'max' => 100],
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

    public static function getByCode($code) {
        //return OrderStatus::find()->where(['code' => $code])->one();

        // убираем лишний запрос в базу
        if($code == 'created') {

            $status = new Status();
            $status->id = 1;
            $status->name = 'Записан';
            $status->code = 'created';

            return $status;

        }elseif($code == 'canceled') {

            $status = new Status();
            $status->id = 2;
            $status->name = 'Отменен';
            $status->code = 'canceled';

            return $status;

        }elseif($code == 'sent') {

            $status = new Status();
            $status->id = 3;
            $status->name = 'Отправлен';
            $status->code = 'sent';

            return $status;
        }
    }
}
