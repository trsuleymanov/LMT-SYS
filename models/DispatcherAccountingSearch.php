<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DispatcherAccounting;
use app\models\User;

/**
 * DispatcherAccountingSearch represents the model behind the search form about `app\models\DispatcherAccounting`.
 */
class DispatcherAccountingSearch extends DispatcherAccounting
{
    public $user_fio;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'dispetcher_id', 'order_id', /*'call_appeal_id'*/], 'integer'],
            [['operation_type', 'user_fio', 'created_at', 'value', 'order_temp_identifier'], 'safe'],
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
        $query = DispatcherAccounting::find()
            ->leftJoin('user', '`user`.`id` = `dispatcher_accounting`.`dispetcher_id`');

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
                'user_fio' => [
                    'asc' => [User::tableName().'.lastname' => SORT_ASC],
                    'desc' => [User::tableName().'.lastname' => SORT_DESC]
                ],
            ])
        ]);



        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            $this->tableName().'.operation_type' => $this->operation_type,
            $this->tableName().'.dispetcher_id' => $this->dispetcher_id,
            //$this->tableName().'created_at' => $this->created_at,
            $this->tableName().'.order_id' => $this->order_id,
            //$this->tableName().'.order_temp_identifier' => $this->order_temp_identifier,
            //$this->tableName().'.call_appeal_id' => $this->call_appeal_id,
        ]);

        if (!is_null($this->created_at) && strpos($this->created_at, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->created_at);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.created_at', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if(strpos($this->user_fio, ' ')) {
            $aFamilyName = explode(' ', $this->user_fio);
            $lastname = $aFamilyName[0];
            $firstname = $aFamilyName[1];
        }else {
            $lastname = $firstname = $this->user_fio;
        }
        $query->andFilterWhere([
            'OR',
            ['like', User::tableName().'.lastname', $lastname],
            ['like', User::tableName().'.firstname', $firstname]
        ]);

        $query->andFilterWhere(['like', 'value', $this->value]);


        return $dataProvider;
    }
}
