<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DoTariff;

/**
 * DoTariffSearch represents the model behind the search form of `app\models\DoTariff`.
 */
class DoTariffSearch extends DoTariff
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['description', 'tariff_type', /*'code',*/ 'order_comment', 'place_price_formula',
                'use_fix_price', 'use_client_do_tariff'], 'safe'],
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
        $query = DoTariff::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'place_price_formula' => $this->place_price_formula,
            'order_price_formula' => $this->order_price_formula,
            'use_fix_price' => $this->use_fix_price,
            'use_client_do_tariff' => $this->use_client_do_tariff,
        ]);

        $query
            ->andFilterWhere(['like', 'description', $this->description])
            //->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'order_comment', $this->order_comment])
            ->andFilterWhere(['like', 'tariff_type', $this->tariff_type]);

        return $dataProvider;
    }
}
