<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Formula;

/**
 * FormulaSearch represents the model behind the search form about `app\models\Formula`.
 */
class FormulaSearch extends Formula
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'integer'],
            [['formula', 'created_at', 'updated_at'], 'safe'],
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
        $query = Formula::find();

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
            'name' => $this->name,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'formula', $this->formula]);

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->updated_at) && strpos($this->updated_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->updated_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.updated_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        return $dataProvider;
    }
}
