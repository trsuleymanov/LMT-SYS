<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CashbackSetting;

/**
 * CashbackSettingSearch represents the model behind the search form of `app\models\CashbackSetting`.
 */
class CashbackSettingSearch extends CashbackSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'start_date', 'order_accrual_percent', /*'order_penalty_percent',*/
                /*'hours_before_start_trip_for_penalty',*/ 'with_commercial_trips',
                'red_penalty_max_time', 'order_red_penalty_percent', 'yellow_penalty_max_time',
                'order_yellow_penalty_percent', 'max_time_confirm_diff', 'max_time_confirm_delta',
            ], 'integer'],
            [['cashback_type'], 'string', 'max' => 20]
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
        $query = CashbackSetting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'start_date' => $this->start_date,
            'order_accrual_percent' => $this->order_accrual_percent,
            //'order_penalty_percent' => $this->order_penalty_percent,
            //'hours_before_start_trip_for_penalty' => $this->hours_before_start_trip_for_penalty,

            'red_penalty_max_time' => $this->red_penalty_max_time,
            'order_red_penalty_percent' => $this->order_red_penalty_percent,
            'yellow_penalty_max_time' => $this->yellow_penalty_max_time,
            'order_yellow_penalty_percent' => $this->order_yellow_penalty_percent,
            'max_time_confirm_diff' => $this->max_time_confirm_diff,
            'max_time_confirm_delta' => $this->max_time_confirm_delta,

            'with_commercial_trips' => $this->with_commercial_trips,
            //'has_cashback_for_prepayment' => $this->has_cashback_for_prepayment,
            //'has_cashback_for_nonprepayment' => $this->has_cashback_for_nonprepayment,
            'cashback_type' => $this->cashback_type,
        ]);

        return $dataProvider;
    }
}
