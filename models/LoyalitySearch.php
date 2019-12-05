<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Loyality;

/**
 * LoyalitySearch represents the model behind the search form of `app\models\Loyality`.
 */
class LoyalitySearch extends Loyality
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'past_sent_orders', 'past_sent_orders_places', 'past_canceled_orders',
                'past_canceled_orders_places', 'past_fixed_price_orders_places', 'past_is_not_places',
                'past_informer_beznal_orders_places', 'past_prize_trip_count', 'past_penalty', 'present_sent_orders',
                'present_sent_orders_places', 'present_canceled_orders', 'present_canceled_orders_places',
                'present_fixed_price_orders_places', 'present_is_not_places', 'present_informer_beznal_orders_places',
                'present_prize_trip_count', 'present_penalty', 'total_sent_orders',
                'total_sent_orders_places', 'total_canceled_orders', 'total_canceled_orders_places',
                'total_fixed_price_orders_places', 'total_is_not_places', 'total_informer_beznal_orders_places',
                'total_prize_trip_count', 'total_penalty'], 'integer'],
            [[
                'past_i1', 'past_i2', 'past_i3', 'past_i4', 'past_i5',
                'present_i1', 'present_i2', 'present_i3', 'present_i4', 'present_i5',
                'total_i1', 'total_i2', 'total_i3', 'total_i4', 'total_i5',
                'loyalty_indicator'], 'number'],

            [['client_id',], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Loyality::find()
            ->leftJoin('client', '`client`.`id` = `loyality`.`client_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'client_id' => $this->client_id,
            'past_sent_orders' => $this->past_sent_orders,
            'past_sent_orders_places' => $this->past_sent_orders_places,
            'past_canceled_orders' => $this->past_canceled_orders,
            'past_canceled_orders_places' => $this->past_canceled_orders_places,
            'past_fixed_price_orders_places' => $this->past_fixed_price_orders_places,
            'past_informer_beznal_orders_places' => $this->past_informer_beznal_orders_places,
            'past_is_not_places' => $this->past_is_not_places,
            'past_prize_trip_count' => $this->past_prize_trip_count,
            'past_penalty' => $this->past_penalty,
            'past_i1' => $this->past_i1,
            'past_i2' => $this->past_i2,
            'past_i3' => $this->past_i3,
            'past_i4' => $this->past_i4,
            'past_i5' => $this->past_i5,

            'present_sent_orders' => $this->present_sent_orders,
            'present_sent_orders_places' => $this->present_sent_orders_places,
            'present_canceled_orders' => $this->present_canceled_orders,
            'present_canceled_orders_places' => $this->present_canceled_orders_places,
            'present_fixed_price_orders_places' => $this->present_fixed_price_orders_places,
            'present_informer_beznal_orders_places' => $this->present_informer_beznal_orders_places,
            'present_is_not_places' => $this->present_is_not_places,
            'present_prize_trip_count' => $this->present_prize_trip_count,
            'present_penalty' => $this->present_penalty,
            'present_i1' => $this->present_i1,
            'present_i2' => $this->present_i2,
            'present_i3' => $this->present_i3,
            'present_i4' => $this->present_i4,
            'present_i5' => $this->present_i5,

            'total_sent_orders' => $this->total_sent_orders,
            'total_sent_orders_places' => $this->total_sent_orders_places,
            'total_canceled_orders' => $this->total_canceled_orders,
            'total_canceled_orders_places' => $this->total_canceled_orders_places,
            'total_fixed_price_orders_places' => $this->total_fixed_price_orders_places,
            'total_informer_beznal_orders_places' => $this->total_informer_beznal_orders_places,
            'total_is_not_places' => $this->total_is_not_places,
            'total_prize_trip_count' => $this->total_prize_trip_count,
            'total_penalty' => $this->total_penalty,
            'total_i1' => $this->total_i1,
            'total_i2' => $this->total_i2,
            'total_i3' => $this->total_i3,
            'total_i4' => $this->total_i4,
            'total_i5' => $this->total_i5,
            'loyalty_indicator' => $this->loyalty_indicator,
        ]);

        $query
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id]);

        return $dataProvider;
    }
}
