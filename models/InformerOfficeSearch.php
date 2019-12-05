<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InformerOffice;

/**
 * InformerOfficeSearch represents the model behind the search form about `app\models\InformerOffice`.
 */
class InformerOfficeSearch extends InformerOffice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'do_tariff_id'], 'integer'],
            [['name', 'code', 'created_at', 'updated_at', 'cashless_payment'], 'safe'],
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
        $query = InformerOffice::find();

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
            'cashless_payment' => $this->cashless_payment,
            'do_tariff_id' => $this->do_tariff_id,
        ]);

        $query
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name]);


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
