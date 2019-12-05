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
            [['id', 't_create', 't_answer', 't_hungup', 'ats_start_time', 'ats_answer_time', 'ats_eok_time',
                'handling_call_operator_id', 'caused_by_missed_call_window'], 'integer'],
            [['call_direction', 'operand', 'ext_tracking_id', 'sip', 'status'], 'safe'],
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
            'caused_by_missed_call_window' => $this->caused_by_missed_call_window,
            't_create' => $this->t_create,
            't_answer' => $this->t_answer,
            't_hungup' => $this->t_hungup,
            'ats_start_time' => $this->ats_start_time,
            'ats_answer_time' => $this->ats_answer_time,
            'ats_eok_time' => $this->ats_eok_time,
            'handling_call_operator_id' => $this->handling_call_operator_id,
        ]);

        $query->andFilterWhere(['like', 'call_direction', $this->call_direction])
            ->andFilterWhere(['like', 'operand', $this->operand])
            ->andFilterWhere(['like', 'ext_tracking_id', $this->ext_tracking_id])
            ->andFilterWhere(['like', 'sip', $this->sip])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
