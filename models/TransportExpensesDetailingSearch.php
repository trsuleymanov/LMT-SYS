<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TransportExpensesDetailing;

/**
 * TransportExpensesDetailingSearch represents the model behind the search form of `app\models\TransportExpensesDetailing`.
 */
class TransportExpensesDetailingSearch extends TransportExpensesDetailing
{
    public $expenses_doc_type_id;
    public $expenses_type_id;
    public $doc_number;
    public $expenses_seller_id;
    public $need_pay_date;
    public $waybill_transport_id;
    public $waybill_driver_id;
    public $waybill_date_of_issue;
    public $waybill_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'expense_id'], 'integer'],
            [['name', 'type', 'expenses_doc_type_id', 'expenses_type_id', 'doc_number', 'expenses_seller_id',
                'need_pay_date', 'waybill_transport_id', 'waybill_transport_id', 'waybill_driver_id',
                'waybill_date_of_issue', 'waybill_number'], 'safe'],
            [['price'], 'number'],
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
        $query = TransportExpensesDetailing::find()
            ->leftJoin('transport_expenses', '`transport_expenses`.`id` = `transport_expenses_detailing`.`expense_id`')
            ->leftJoin('transport_waybill', '`transport_waybill`.`id` = `transport_expenses`.`transport_waybill_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'waybill_date_of_issue'  => SORT_DESC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'waybill_number' => [
                    'asc' => [TransportWaybill::tableName().'.number' => SORT_ASC],
                    'desc' => [TransportWaybill::tableName().'.number' => SORT_DESC]
                ],
                'waybill_date_of_issue' => [
                    'asc' => [TransportWaybill::tableName().'.date_of_issue' => SORT_ASC],
                    'desc' => [TransportWaybill::tableName().'.date_of_issue' => SORT_DESC]
                ],
                'waybill_transport_id' => [
                    'asc' => [TransportWaybill::tableName().'.transport_id' => SORT_ASC],
                    'desc' => [TransportWaybill::tableName().'.transport_id' => SORT_DESC]
                ],
                'waybill_driver_id' => [
                    'asc' => [TransportWaybill::tableName().'.driver_id' => SORT_ASC],
                    'desc' => [TransportWaybill::tableName().'.driver_id' => SORT_DESC]
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
            $this->tableName().'.expense_id' => $this->expense_id,
            $this->tableName().'.price' => $this->price,
            //'expenses_doc_type_id' => $this->expenses_doc_type_id,
            TransportExpenses::tableName().'.expenses_doc_type_id' => $this->expenses_doc_type_id,
            TransportExpenses::tableName().'.expenses_type_id' => $this->expenses_type_id,
            TransportExpenses::tableName().'.expenses_seller_id' => $this->expenses_seller_id,
            //TransportExpenses::tableName().'.doc_number' => $this->doc_number,
            TransportWaybill::tableName().'.transport_id' => $this->waybill_transport_id,
            TransportWaybill::tableName().'.driver_id' => $this->waybill_driver_id,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', TransportExpenses::tableName().'.doc_number', $this->doc_number])
            ->andFilterWhere(['like', TransportWaybill::tableName().'.number', $this->waybill_number])
            ->andFilterWhere(['like', 'type', $this->type]);


        if (!is_null($this->need_pay_date) && strpos($this->need_pay_date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->need_pay_date);
            $query->andFilterWhere([
                'BETWEEN', TransportExpenses::tableName().'.need_pay_date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->waybill_date_of_issue) && strpos($this->waybill_date_of_issue, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->waybill_date_of_issue);
            $query->andFilterWhere([
                'BETWEEN', TransportWaybill::tableName().'.date_of_issue', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
