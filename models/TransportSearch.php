<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transport;

/**
 * TransportSearch represents the model behind the search form about `app\models\Transport`.
 */
class TransportSearch extends Transport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'places_count', 'base_city_id'], 'integer'],
            [['model', 'sh_model', 'car_reg', 'color', 'created_at', 'updated_at'], 'safe'],
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
        $query = Transport::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            //'defaultOrder' => ['date' => SORT_DESC],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'car_reg' => [
                    //'asc' => [Transport::tableName().'.car_reg' => SORT_ASC],
                    //'desc' => [Transport::tableName().'.car_reg' => SORT_DESC]
                    'asc' => ['CONVERT(car_reg,SIGNED)' => SORT_ASC],
                    'desc' => ['CONVERT(car_reg,SIGNED)' => SORT_DESC],
                ],
            ])
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'base_city_id' => $this->base_city_id
        ]);

        $query->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'sh_model', $this->sh_model])
            ->andFilterWhere(['like', 'car_reg', $this->car_reg])
            ->andFilterWhere(['like', 'places_count', $this->places_count])
            ->andFilterWhere(['like', 'color', $this->color]);

//        if (!empty($this->created_at)) {
//            $created_at = strtotime($this->created_at);
//            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
//        }
//        if (!empty($this->updated_at)) {
//            $updated_at = strtotime($this->updated_at);
//            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
//        }
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
