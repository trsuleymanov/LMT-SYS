<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CallEvent;

/**
 * CallEventSearch represents the model behind the search form of `app\models\CallEvent`.
 */
class CallEventSearch extends CallEvent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'call_id', 'operator_user_id', 'event_time'], 'integer'],
            [['operator_sip', 'event'], 'safe'],
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
        $query = CallEvent::find();

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
            'call_id' => $this->call_id,
            'operator_user_id' => $this->operator_user_id,
            'event_time' => $this->event_time,
        ]);

        $query->andFilterWhere(['like', 'operator_sip', $this->operator_sip])
            ->andFilterWhere(['like', 'event', $this->event]);

        return $dataProvider;
    }
}
