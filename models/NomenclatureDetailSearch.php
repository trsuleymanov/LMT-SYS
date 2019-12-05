<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NomenclatureDetail;

/**
 * NomenclatureDetailSearch represents the model behind the search form of `app\models\NomenclatureDetail`.
 */
class NomenclatureDetailSearch extends NomenclatureDetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'model_id', 'installation_place', 'installation_side', 'measurement_value_id'], 'integer'],
            [['temp_name', 'comment', 'detail_code', 'measurement_value_id', 'detail_name_id'], 'safe'],
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
        $query = NomenclatureDetail::find()
            ->leftJoin('detail_name', '`detail_name`.`id` = `nomenclature_detail`.`detail_name_id`');

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
            'model_id' => $this->model_id,
            'installation_place' => $this->installation_place,
            'installation_side' => $this->installation_side,
            'measurement_value_id' => $this->measurement_value_id
        ]);

        $query
            //->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', '`detail_name`.name', $this->temp_name])
            ->andFilterWhere(['like', 'detail_code', $this->detail_code])
//            ->andFilterWhere(['like', 'measurement_value_id', $this->measurement_value])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
