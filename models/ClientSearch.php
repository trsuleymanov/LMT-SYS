<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;

/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    public $rating_from;
    public $rating_to;
    public $sended_orders_places_count_from;
    public $sended_orders_places_count_to;
    public $sended_prize_trip_count_from;
    public $sended_prize_trip_count_to;
    public $penalty_from;
    public $penalty_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'rating', 'sended_orders_places_count', 'sended_prize_trip_count', 'penalty', 'do_tariff_id'], 'integer'],
            [['name', 'mobile_phone', 'home_phone', 'alt_phone', 'created_at', 'updated_at',
                'rating_from', 'rating_to', 'sended_orders_places_count_from', 'sended_orders_places_count_to', 'sended_prize_trip_count_from',
                'sended_prize_trip_count_to', 'penalty_from', 'penalty_to',

                'current_year_sended_places', 'current_year_sended_orders', 'current_year_canceled_places',
                'current_year_canceled_orders',
                'current_year_canceled_orders_1h', 'current_year_canceled_orders_12h',
                //'current_year_places_reliability', 'current_year_orders_reliability',
                'current_year_sended_prize_places', 'current_year_penalty', 'current_year_sended_fixprice_places',
                'current_year_sended_fixprice_orders', 'current_year_sended_informer_beznal_places',
                'current_year_sended_informer_beznal_orders', 'current_year_sended_isnotplaces_orders',

                'past_years_sended_places', 'past_years_sended_orders', 'past_years_canceled_places',
                'past_years_canceled_orders',
                'past_years_canceled_orders_1h', 'past_years_canceled_orders_12h',

                'past_years_sended_prize_places', 'past_years_penalty',
                'past_years_sended_fixprice_places', 'past_years_sended_fixprice_orders',
                'past_years_sended_informer_beznal_places', 'past_years_sended_informer_beznal_orders',
                'past_years_sended_isnotplaces_orders',

                'cashback', 'sync_date'

            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Client::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false
            'pagination' => [
                //'pageSize' => 20,
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'do_tariff_id' => $this->do_tariff_id,
            'cashback' => $this->cashback,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'home_phone', $this->home_phone])
            ->andFilterWhere(['like', 'alt_phone', $this->alt_phone]);

//        if (!empty($this->created_at)) {
//            $created_at = strtotime($this->created_at);
//            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
//        }
//        if (!empty($this->updated_at)) {
//            $updated_at = strtotime($this->updated_at);
//            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
//        }

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->updated_at) && strpos($this->updated_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->updated_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.updated_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!empty($this->rating_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.rating', $this->rating_from]);
        }
        if (!empty($this->rating_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.rating', $this->rating_to]);
        }

        if (!empty($this->sended_orders_places_count_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.sended_orders_places_count', $this->sended_orders_places_count_from]);
        }
        if (!empty($this->sended_orders_places_count_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.sended_orders_places_count', $this->sended_orders_places_count_to]);
        }

        if (!empty($this->sended_prize_trip_count_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.sended_prize_trip_count', $this->sended_prize_trip_count_from]);
        }
        if (!empty($this->sended_prize_trip_count_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.sended_prize_trip_count', $this->sended_prize_trip_count_to]);
        }

        if (!empty($this->penalty_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.penalty', $this->penalty_from]);
        }
        if (!empty($this->penalty_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.penalty', $this->penalty_to]);
        }

        return $dataProvider;
    }
}
