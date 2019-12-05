<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TripOperation;

/**
 * TripOperationSearch represents the model behind the search form of `app\models\TripOperation`.
 */
class TripOperationSearch extends TripOperation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'delta'], 'integer'],
            [['type', 'comment', 'created_at'], 'safe'],
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
        $query = TripOperation::find();

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
            //'created_at' => $this->created_at,
            'user_id' => $this->user_id,
            'delta' => $this->delta,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
