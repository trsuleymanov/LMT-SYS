<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdvertisingSourceReport;

/**
 * AdvertisingSourceReportSearch represents the model behind the search form of `app\models\AdvertisingSourceReport`.
 */
class AdvertisingSourceReportSearch extends AdvertisingSourceReport
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'advertising_source_id', 'operator_user_id', 'client_id',], 'integer'],
            [['created_at', 'phone'], 'safe']
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
        $query = AdvertisingSourceReport::find();

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
            'advertising_source_id' => $this->advertising_source_id,
            'operator_user_id' => $this->operator_user_id,
            'client_id' => $this->client_id,
            //'created_at' => $this->created_at,
            'phone' => $this->phone,
        ]);

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
