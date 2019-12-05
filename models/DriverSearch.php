<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Driver;

/**
 * DriverSearch represents the model behind the search form about `app\models\Driver`.
 */
class DriverSearch extends Driver
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'primary_transport_id', 'secondary_transport_id', 'user_id'], 'integer'],
            [['fio', 'mobile_phone', 'home_phone', 'created_at', 'updated_at', 'device_code'], 'safe'],
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
        $query = Driver::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' => false
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'user_id' => $this->user_id,
            'primary_transport_id' => $this->primary_transport_id,
            'secondary_transport_id' => $this->secondary_transport_id,
        ]);

        $query->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'home_phone', $this->home_phone])
            ->andFilterWhere(['like', 'device_code', $this->device_code]);

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
