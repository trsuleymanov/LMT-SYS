<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\City;

/**
 * CitySearch represents the model behind the search form about `app\models\City`.
 */
class CitySearch extends City
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'map_scale', 'search_scale', 'point_focusing_scale', 'all_points_show_scale',
                'extended_external_use'], 'integer'],
            [['center_lat', 'center_long'], 'number'],
            [['name', 'created_at', 'updated_at'], 'safe'],
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
        $query = City::find();

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
            'extended_external_use' => $this->extended_external_use,
            'center_lat' => $this->center_lat,
            'center_long' => $this->center_long,
            'map_scale' => $this->map_scale,
            'search_scale' => $this->search_scale,
            'point_focusing_scale' => $this->point_focusing_scale,
            'all_points_show_scale' => $this->all_points_show_scale,
        ]);

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

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
