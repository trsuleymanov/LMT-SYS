<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\YandexPoint;
use yii\helpers\ArrayHelper;


class YandexPointSearch extends YandexPoint
{
    public $trip_date;
    public $orders_total_count; // всего заказов
    public $places_total_count; // всего мест в заказах

    public $orders_sended_count; // заказов отправлено
    public $places_sended_count; // пассажиров отправлено

    public $orders_canceled_count; // заказов отменено
    public $places_canceled_count; // пассажиров отменено

    public $empty;

    public $child_orders_sended_count; // заказов с детьми отправлено
    public $child_places_sended_count; // детей отправлено

    public $child_orders_canceled_count; // заказов с детьми отменено
    public $child_places_canceled_count; // детей отменено

    public $empty2;

    public $student_orders_sended_count; // заказов со студентами отправлено
    public $student_places_sended_count; // студентов отправлено

    public $student_orders_canceled_count; // заказов со студентами отменено
    public $student_places_canceled_count; // студентов отменено



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city_id', 'critical_point', 'external_use', 'super_tariff_used',
                'point_from_standart_price_diff', 'point_from_commercial_price_diff',
                'point_to_standart_price_diff', 'point_to_commercial_price_diff', 'active'
            ], 'integer'],
            [['lat', 'long'], 'double'],
            [['name', 'description'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'creator_id', 'updater_id', 'point_of_arrival', 'sync_date',
                'popular_departure_point', 'popular_arrival_point',
            ], 'safe'],

            [[
                'trip_date',
                'orders_total_count', 'places_total_count',
                'orders_sended_count', 'places_sended_count',
                'orders_canceled_count', 'places_canceled_count',
                'empty',
                'child_orders_sended_count', 'child_places_sended_count',
                'child_orders_canceled_count', 'child_places_canceled_count',
                'empty2',
                'student_orders_sended_count', 'student_places_sended_count',
                'student_orders_canceled_count', 'student_places_canceled_count'

            ], 'safe'],
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
            'point_from_standart_price_diff' => $this->point_from_standart_price_diff,
            'point_from_commercial_price_diff' => $this->point_from_commercial_price_diff,
            'point_to_standart_price_diff' => $this->point_to_standart_price_diff,
            'point_to_commercial_price_diff' => $this->point_to_commercial_price_diff,
            'active' => $this->active,
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



    public function searchStatistic($params)
    {
        $query = YandexPoint::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => Yii::$app->session->get('table-rows', 20)
                'pageSize' => 500
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'city_id' => $this->city_id,
        ]);


        $query->andFilterWhere(['like', 'name', $this->name]);


        return $dataProvider;
    }
}
