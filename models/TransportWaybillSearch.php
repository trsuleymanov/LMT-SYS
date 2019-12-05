<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TransportWaybill;

/**
 * TransportWaybillSearch represents the model behind the search form of `app\models\TransportWaybill`.
 */
class TransportWaybillSearch extends TransportWaybill
{
    public $tovxrash;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'transport_id', 'driver_id', 'pre_trip_med_check', 'pre_trip_med_check_time', 'pre_trip_tech_check',
                'pre_trip_tech_check_time', 'after_trip_med_check', 'after_trip_med_check_time', 'after_trip_tech_check',
                'after_trip_tech_check_time', 'mileage_before_departure', 'mileage_after_departure',
                'departure_time', 'return_time', 'trip_transport_start', 'trip_transport_end', 'created_at', 'creator_id',
                'set_hand_over_b1_operator_id', 'set_hand_over_b2_operator_id',
                ], 'integer'],
            [['number', 'changes_history', 'date_of_issue',  'waybill_state', 'values_fixed_state',
                'gsm', 'klpto', 'klpto_comment',
                'trip_event1_id', 'trip_event1_comment', 'trip_event2_id', 'trip_event2_comment',
                'trip_event3_id', 'trip_event3_comment', 'trip_event4_id', 'trip_event4_comment',
                'trip_event5_id', 'trip_event5_comment', 'trip_event6_id', 'trip_event6_comment',
                'trip_event7_id', 'trip_event7_comment', 'trip_event8_id', 'trip_event8_comment',
                'tovxrash', 'is_visible',
                'hand_over_b1_data', 'hand_over_b2_data', 'set_hand_over_b2_time', 'set_hand_over_b1_time',
            ], 'safe'],
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
        $query = TransportWaybill::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'date_of_issue'  => SORT_DESC,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //echo "<pre>"; print_r($this); echo "</pre>"; exit;

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            'id' => $this->getTovxrash(),
            //'is_visible' => $this->is_visible,
            //'date_of_issue' => $this->date_of_issue,
            'transport_id' => $this->transport_id,
            'driver_id' => $this->driver_id,
            'pre_trip_med_check' => $this->pre_trip_med_check,
            'pre_trip_med_check_time' => $this->pre_trip_med_check_time,
            'pre_trip_tech_check' => $this->pre_trip_tech_check,
            'pre_trip_tech_check_time' => $this->pre_trip_tech_check_time,
            'after_trip_med_check' => $this->after_trip_med_check,
            'after_trip_med_check_time' => $this->after_trip_med_check_time,
            'after_trip_tech_check' => $this->after_trip_tech_check,
            'after_trip_tech_check_time' => $this->after_trip_tech_check_time,
            'mileage_before_departure' => $this->mileage_before_departure,
            'mileage_after_departure' => $this->mileage_after_departure,
            'departure_time' => $this->departure_time,
            'return_time' => $this->return_time,
            'trip_transport_start' => $this->trip_transport_start,
            'trip_transport_end' => $this->trip_transport_end,
            'created_at' => $this->created_at,
            'creator_id' => $this->creator_id,

            'waybill_state' => $this->waybill_state,
            'values_fixed_state' => $this->values_fixed_state,
            'gsm' => $this->gsm,
            'klpto' => $this->klpto,
            'klpto_comment' => $this->klpto_comment,

            'trip_event1_id' => $this->trip_event1_id,
            'trip_event1_comment' => $this->trip_event1_comment,

            'trip_event2_id' => $this->trip_event2_id,
            'trip_event2_comment' => $this->trip_event2_comment,

            'trip_event3_id' => $this->trip_event3_id,
            'trip_event3_comment' => $this->trip_event3_comment,

            'trip_event4_id' => $this->trip_event4_id,
            'trip_event4_comment' => $this->trip_event4_comment,

            'trip_event5_id' => $this->trip_event5_id,
            'trip_event5_comment' => $this->trip_event5_comment,

            'trip_event6_id' => $this->trip_event6_id,
            'trip_event6_comment' => $this->trip_event6_comment,

            'trip_event7_id' => $this->trip_event7_id,
            'trip_event7_comment' => $this->trip_event7_comment,

            'trip_event8_id' => $this->trip_event8_id,
            'trip_event8_comment' => $this->trip_event8_comment,

            //'mileage_dif' => ''
            //'consumption_per_100_km' => '',

            //'set_hand_over_b1_time' => $this->set_hand_over_b1_time,
            'set_hand_over_b1_operator_id' => $this->set_hand_over_b1_operator_id,
            'set_hand_over_b2_operator_id' => $this->set_hand_over_b2_operator_id,
            //'set_hand_over_b2_time' => $this->set_hand_over_b2_time,
        ]);

        if (!is_null($this->date_of_issue) && strpos($this->date_of_issue, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date_of_issue);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date_of_issue', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->hand_over_b1_data) && strpos($this->hand_over_b1_data, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->hand_over_b1_data);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.hand_over_b1_data', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->set_hand_over_b1_time) && strpos($this->set_hand_over_b1_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->set_hand_over_b1_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.set_hand_over_b1_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->hand_over_b2_data) && strpos($this->hand_over_b2_data, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->hand_over_b2_data);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.hand_over_b2_data', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->set_hand_over_b2_time) && strpos($this->set_hand_over_b2_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->set_hand_over_b2_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.set_hand_over_b2_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'changes_history', $this->changes_history]);

        if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            $query->andFilterWhere(['is_visible' => 1]);
        }else {

            if($this->is_visible === '0' || $this->is_visible === '1') {
                if($this->is_visible === '0') {
                    $query->andWhere([
                        'OR',
                        ['is_visible' => 0],
                        ['is_visible' => NULL],
                    ]);
                }else {
                    $query->andFilterWhere(['is_visible' => 1]);
                }
            }
        }

        return $dataProvider;
    }
}
