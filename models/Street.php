<?php

namespace app\models;

use Yii;
use app\models\City;
use app\models\Order;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "street".
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $name
 */
class Street extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'street';
    }

    public static function getDefaultStreet($city_id) {

        if ($city_id == 1) { // Казань
            return Street::findOne(1);
        }elseif($city_id == 2) { // Альметьевск
            return Street::findOne(2);
        }else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id'], 'integer'],
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
            'city_id' => 'Город',
            'name' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }


    // тут функции работают с городами и улицами с определенными id: Казань - id=1, Альметьевск - id=2
    // и улица по умолчанию для Казани = id=1, для Альметьевска = id=2

    public function beforeDelete()
    {
        // если улица используется в заказе, то прерывается удаление
        $order = Order::find()->where([
            'OR',
            ['street_id_from' => $this->id],
            ['street_id_to' => $this->id]
        ])->one();

        if($order != null) {
            throw new ForbiddenHttpException('Нельзя удалить улицу, которая использована в заказах');
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}
