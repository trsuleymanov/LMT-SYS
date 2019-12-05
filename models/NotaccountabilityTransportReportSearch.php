<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NotaccountabilityTransportReport;

/**
 * NotaccountabilityTransportReportSearch represents the model behind the search form of `app\models\NotaccountabilityTransportReport`.
 */
class NotaccountabilityTransportReportSearch extends NotaccountabilityTransportReport
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'date_start_circle', 'transport_id', 'driver_id', 'trip_transport_start', 'trip_transport_end',
                'set_hand_over_operator_id', 'day_report_transport_circle_id'], 'integer'],
            [['hand_over', 'set_hand_over_time', 'formula_percent', 'date_start_circle', 'date_end_circle'], 'safe'],
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
        $query = NotaccountabilityTransportReport::find();

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
            //'date_of_issue' => $this->date_of_issue,
            'transport_id' => $this->transport_id,
            'driver_id' => $this->driver_id,
            'day_report_transport_circle_id' => $this->day_report_transport_circle_id,
            'trip_transport_start' => $this->trip_transport_start,
            'trip_transport_end' => $this->trip_transport_end,
            'hand_over' => $this->hand_over,
            'formula_percent' => $this->formula_percent,
            //'hand_over_b1_data' => $this->hand_over_b1_data,
            'set_hand_over_operator_id' => $this->set_hand_over_operator_id,
            //'set_hand_over_b1_time' => $this->set_hand_over_b1_time,
        ]);


        if (!is_null($this->date_start_circle) && strpos($this->date_start_circle, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date_start_circle);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date_start_circle', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->date_end_circle) && strpos($this->date_end_circle, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date_end_circle);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date_end_circle', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

//        if (!is_null($this->hand_over_data) && strpos($this->hand_over_data, '-') !== false) {
//            list($dateStart, $dateEnd) = explode('-', $this->hand_over_data);
//            $query->andFilterWhere([
//                'BETWEEN', $this->tableName() . '.hand_over_data', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
//            ]);
//        }
        if (!is_null($this->set_hand_over_time) && strpos($this->set_hand_over_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->set_hand_over_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.set_hand_over_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        return $dataProvider;
    }
}
