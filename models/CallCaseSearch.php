<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CallCase;

/**
 * CallCaseSearch represents the model behind the search form of `app\models\CallCase`.
 */
class CallCaseSearch extends CallCase
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'open_time', 'update_time', 'call_count', 'close_time'], 'integer'],
            [['case_type', 'status', 'operand',], 'safe'],
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
        $query = CallCase::find();

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
            'order_id' => $this->order_id,
            'open_time' => $this->open_time,
            'update_time' => $this->update_time,
            'call_count' => $this->call_count,
            'close_time' => $this->close_time,
        ]);

        $query->andFilterWhere(['like', 'case_type', $this->case_type])
            ->andFilterWhere(['like', 'operand', $this->operand])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
