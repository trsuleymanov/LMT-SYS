<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StorageDetail;

/**
 * StorageDetailSearch represents the model behind the search form of `app\models\StorageDetail`.
 */
class StorageDetailSearch extends StorageDetail
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'storage_id',
                'detail_state_id', 'detail_origin_id', 'storage_place_count', 'remainder',], 'integer'],
            [[
                'comment', 'nomenclature_detail_id',
                'created_at', 'updated_at',], 'safe'],
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
        $query = StorageDetail::find()
            ->leftJoin('nomenclature_detail', '`nomenclature_detail`.`id` = `storage_detail`.`nomenclature_detail_id`')
            ->leftJoin('detail_name', '`detail_name`.`id` = `nomenclature_detail`.`detail_name_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 20,
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'nomenclature_detail_id' => [
                    'asc' => ['`detail_name`.`name`' => SORT_ASC],
                    'desc' => ['`detail_name`.`name`' => SORT_DESC]
                ],
            ])
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            $this->tableName().'.storage_id' => $this->storage_id,
            //'nomenclature_detail_id' => $this->nomenclature_detail_id,
            $this->tableName().'.detail_state_id' => $this->detail_state_id,
            $this->tableName().'.detail_origin_id' => $this->detail_origin_id,
            $this->tableName().'.storage_place_count' => $this->storage_place_count,
            $this->tableName().'.remainder' => $this->remainder,
        ]);

        $query
            //->andFilterWhere(['>', 'remainder', 0])
            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', '`detail_name`.name', $this->nomenclature_detail_id]);

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
