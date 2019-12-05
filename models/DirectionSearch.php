<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Direction;

/**
 * DirectionSearch represents the model behind the search form about `app\models\Direction`.
 */
class DirectionSearch extends Direction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'distance'], 'integer'],
            [['sh_name', 'created_at', 'updated_at', 'city_from', 'city_to', ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Direction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'city_from' => $this->city_from,
            'city_to' => $this->city_to,
        ]);

        $query->andFilterWhere(['like', 'sh_name', $this->sh_name]);
        $query->andFilterWhere(['like', 'distance', $this->distance]);

        if (!empty($this->created_at)) {
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
        }
        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

//        if (!empty($this->distance)) {
//
//        }

        return $dataProvider;
    }
}
