<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\YandexPoint;
use yii\helpers\ArrayHelper;


class YandexPointSearch extends YandexPoint
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'critical_point', 'external_use', 'super_tariff_used',
                'standart_price_diff', 'commercial_price_diff'], 'integer'],
            [['lat', 'long'], 'double'],
            [['name', 'description'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'creator_id', 'updater_id', 'point_of_arrival', 'sync_date',
                'popular_departure_point', 'popular_arrival_point'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = YandexPoint::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
            'lat' => $this->lat,
            'long' => $this->long,
            'point_of_arrival' => $this->point_of_arrival,
            'critical_point' => $this->critical_point,
            'super_tariff_used' => $this->super_tariff_used,
            'popular_departure_point' => $this->popular_departure_point,
            'popular_arrival_point' => $this->popular_arrival_point,
            'external_use' => $this->external_use,
            'standart_price_diff' => $this->standart_price_diff,
            'commercial_price_diff' => $this->commercial_price_diff,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        //echo 'this:<pre>'; print_r($this); echo '</pre>';

        // здесь creator_id - это часть строки в фио пользователя, нужны соответствующие пользователи
        if(!empty($this->creator_id)) {
            $users = User::find()->where([
                'or',
                ['like', 'firstname', $this->creator_id],
                ['like', 'lastname', $this->creator_id]
            ])->all();
            $query->andFilterWhere(['creator_id' => ArrayHelper::map($users, 'id', 'id')]);
        }

        if(!empty($this->updater_id)) {
            $users = User::find()->where([
                'or',
                ['like', 'firstname', $this->updater_id],
                ['like', 'lastname', $this->updater_id]
            ])->all();
            $query->andFilterWhere(['updater_id' => ArrayHelper::map($users, 'id', 'id')]);
        }


        if (!empty($this->created_at)) {
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
        }
        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        return $dataProvider;
    }
}
