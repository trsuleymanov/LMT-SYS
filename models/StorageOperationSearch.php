<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StorageOperation;

/**
 * StorageOperationSearch represents the model behind the search form of `app\models\StorageOperation`.
 */
class StorageOperationSearch extends StorageOperation
{
    public $nomenclature_detail_name;
    public $storage_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'transport_id', 'driver_id', 'operation_type_id', ], 'integer'],
            [['comment', 'storage_detail_id', 'date', 'created_at', 'creator_id', 'count',
                'nomenclature_detail_name', 'storage_id'], 'safe'],
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
        // storage_detail
        $query = StorageOperation::find()
            ->leftJoin('storage_detail', '`storage_detail`.`id` = `storage_operation`.`storage_detail_id`')
            ->leftJoin('nomenclature_detail', '`nomenclature_detail`.`id` = `storage_detail`.`nomenclature_detail_id`')
            ->leftJoin('user', '`user`.`id` = `storage_operation`.`creator_id`')
            ->leftJoin('detail_name', '`detail_name`.`id` = `nomenclature_detail`.`detail_name_id`');
            //->leftJoin('storage', '`storage`.`id` = `storage_detail`.`storage_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 20,
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            StorageOperation::tableName().'.id' => $this->id,
            //'storage_detail_id' => $this->storage_detail_id,
            StorageOperation::tableName().'.count' => $this->count,
            StorageOperation::tableName().'.transport_id' => $this->transport_id,
            StorageOperation::tableName().'.driver_id' => $this->driver_id,
            //'created_at' => $this->created_at,
            StorageOperation::tableName().'.operation_type_id' => $this->operation_type_id,
            //'creator_id' => $this->creator_id
            StorageDetail::tableName().'.storage_id' => $this->storage_id
        ]);

        $query
            ->andFilterWhere(['like', '`storage_operation`.comment', $this->comment])
            ->andFilterWhere(['like', 'CONCAT(`user`.lastname, " ", `user`.firstname)', $this->creator_id])
            //->andFilterWhere(['like', '`nomenclature_detail`.name', $this->storage_detail_id]);
            ->andFilterWhere(['like', '`detail_name`.name', $this->nomenclature_detail_name]);

        if (!is_null($this->date) && strpos($this->date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
