<?php

namespace app\models;

use Yii;
use app\models\City;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "point".
 *
 * @property integer $id
 * @property string $name
 * @property integer $city_id
 * @property string $alias
 * @property integer $point_of_arrival
 * @property string $critical_point
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $active
 */
class Point extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'city_id'], 'required'],
            [['city_id', 'point_of_arrival', 'created_at', 'updated_at', 'critical_point', 'active'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 50],
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
            'city_id' => 'Город',
            'alias' => 'Псевдоним (на английском)',
            'point_of_arrival' => 'Является точкой прибытия',
            'critical_point' => 'Является критической точкой',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'active' => 'Активна',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->active = 1;
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function beforeDelete()
    {
        // если ориентир(точка) используется в заказе, то прерывается удаление
        $order = Order::find()->where([
            'OR',
            ['point_id_from' => $this->id],
            ['point_id_to' => $this->id]
        ])->one();

        if($order != null) {
            throw new ForbiddenHttpException('Нельзя удалить ориентир, который использован в заказах');
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}
