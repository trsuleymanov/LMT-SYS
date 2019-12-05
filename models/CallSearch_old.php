<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Call;

/**
 * CallSearch represents the model behind the search form of `app\models\Call`.
 */
class CallSearch extends Call
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'finished_at', 'call_from_operator', 'ats_start_time',
                'ats_finish_time', 'ats_answer_time', 'answered_at', 'call_contact_id', ], 'integer'],
            [['ext_tracking_id', 'client_phone', 'status', 'handling_call_operator_id'], 'safe'],
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
        $query = Call::find();

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
            'call_contact_id' => $this->call_contact_id,
            //'created_at' => $this->created_at,
            //'finished_at' => $this->finished_at,
            'call_from_operator' => $this->call_from_operator,
            //'ats_start_time' => $this->ats_start_time,
            //'ats_finish_time' => $this->ats_finish_time,
            'status' => $this->status,
            'handling_call_operator_id' => $this->handling_call_operator_id,
        ]);

        $query->andFilterWhere(['like', 'ext_tracking_id', $this->ext_tracking_id])
            ->andFilterWhere(['like', 'client_phone', $this->client_phone]);
            //->andFilterWhere(['like', 'ats_user_id', $this->ats_user_id]);

//        if (!is_null($this->ats_start_time) && strpos($this->ats_start_time, '-') !== false) {
//            list($dateStart, $dateEnd) = explode('-', $this->ats_start_time);
//            $query->andFilterWhere([
//                'BETWEEN', $this->tableName() . '.ats_start_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
//            ]);
//        }
//
//        if (!is_null($this->ats_finish_time) && strpos($this->ats_finish_time, '-') !== false) {
//            list($dateStart, $dateEnd) = explode('-', $this->ats_finish_time);
//            $query->andFilterWhere([
//                'BETWEEN', $this->tableName() . '.ats_finish_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
//            ]);
//        }

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->answered_at) && strpos($this->answered_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->answered_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.answered_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->finished_at) && strpos($this->finished_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->finished_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.finished_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
