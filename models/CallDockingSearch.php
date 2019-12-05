<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CallDocking;

/**
 * CallDockingSearch represents the model behind the search form of `app\models\CallDocking`.
 */
class CallDockingSearch extends CallDocking
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'call_id', 'case_id', 'conformity'], 'integer'],
            [['click_event'], 'safe'],
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
        $query = CallDocking::find();

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
            'case_id' => $this->case_id,
            'conformity' => $this->conformity,
        ]);

        $query->andFilterWhere(['like', 'click_event', $this->click_event]);

        return $dataProvider;
    }
}
