<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TransportExpensesSearch represents the model behind the search form of `app\models\TransportExpenses`.
 */
class TransportExpensesSearch extends TransportExpenses
{
    public $waybill_number;
    public $waybill_date_of_issue;
    public $waybill_transport_id;
    public $waybill_driver_id;
    public $is_payed;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'transport_waybill_id', 'expenses_doc_type_id', 'expenses_type_id', 'expenses_seller_type_id',
                'check_attached', 'count', 'expenses_is_taken', 'payment_method_id',
                'created_at',
                'creator_id', 'updated_at', 'updator_id'], 'integer'],
            [['price', 'points'], 'number'],
            [['doc_number', 'expenses_is_taken_comment', 'payment_comment', 'view_group',
                'waybill_number', 'waybill_date_of_issue', 'waybill_transport_id', 'waybill_driver_id',
                'expenses_seller_id', 'need_pay_date', 'payment_date', 'transport_expenses_paymenter_id',
                'is_payed'], 'safe'],


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
    public function search($params, $dop_param = '')
    {
        $query = TransportExpenses::find()
            ->leftJoin('transport_waybill', '`transport_waybill`.`id` = `transport_expenses`.`transport_waybill_id`');
            //->leftJoin('transport_expenses', '`transport_expenses`.`id` = `transport_expenses`.`expenses_seller_id`');

        if($dop_param == 'only_with_price') {
            $query->where(['>', 'price', 0]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'waybill_date_of_issue' => SORT_DESC,
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
            $this->tableName().'.transport_waybill_id' => $this->transport_waybill_id,
            $this->tableName().'.expenses_doc_type_id' => $this->expenses_doc_type_id,
            $this->tableName().'.expenses_type_id' => $this->expenses_type_id,
            $this->tableName().'.expenses_seller_type_id' => $this->expenses_seller_type_id,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.check_attached' => $this->check_attached,
            $this->tableName().'.expenses_seller_id' => $this->expenses_seller_id,
            $this->tableName().'.count' => $this->count,
            $this->tableName().'.points' => $this->points,
            //$this->tableName().'.expenses_is_taken' => $this->expenses_is_taken,
            $this->tableName().'.payment_method_id' => $this->payment_method_id,
            //$this->tableName().'.need_pay_date' => $this->need_pay_date,
            //$this->tableName().'.payment_date' => $this->payment_date,
            $this->tableName().'.transport_expenses_paymenter_id' => $this->transport_expenses_paymenter_id,
            $this->tableName().'.created_at' => $this->created_at,
            $this->tableName().'.creator_id' => $this->creator_id,
            $this->tableName().'.updated_at' => $this->updated_at,
            $this->tableName().'.updator_id' => $this->updator_id,

            TransportWaybill::tableName().'.transport_id' => $this->waybill_transport_id,
            TransportWaybill::tableName().'.driver_id' => $this->waybill_driver_id,
        ]);

        if(isset($this->expenses_is_taken)) {
            if($this->expenses_is_taken == "") {

            }elseif($this->expenses_is_taken == 0) {
                //exit('test');
                $query->andWhere([
                    'OR',
                    [$this->tableName() . '.expenses_is_taken' => NULL],
                    [$this->tableName() . '.expenses_is_taken' => 0],
                ]);
            }else {
                $query->andWhere([$this->tableName() . '.expenses_is_taken' => $this->expenses_is_taken]);
            }
        }

        $query
            ->andFilterWhere(['like', $this->tableName().'.doc_number', $this->doc_number])
            ->andFilterWhere(['like', $this->tableName().'.expenses_is_taken_comment', $this->expenses_is_taken_comment])
            ->andFilterWhere(['like', $this->tableName().'.payment_comment', $this->payment_comment])
            ->andFilterWhere(['like', TransportWaybill::tableName().'.number', $this->waybill_number])
            //->andFilterWhere(['like', TransportExpenses::tableName().'.name', $this->expenses_seller_id])
            ->andFilterWhere(['like', $this->tableName().'.view_group', $this->view_group]);

        if (!is_null($this->waybill_date_of_issue) && strpos($this->waybill_date_of_issue, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->waybill_date_of_issue);
            $query->andFilterWhere([
                'BETWEEN', TransportWaybill::tableName().'.date_of_issue', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->need_pay_date) && strpos($this->need_pay_date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->need_pay_date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.need_pay_date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->payment_date) && strpos($this->payment_date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->payment_date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.payment_date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if($this->is_payed == 'is_paid') {
            $query
                ->andFilterWhere(['>', $this->tableName().'.payment_date', 0])
                ->andFilterWhere(['>', $this->tableName().'.payment_method_id', 0])
                ->andFilterWhere(['>', $this->tableName().'.transport_expenses_paymenter_id', 0]);
        }else if($this->is_payed == 'not_paid')  {
            //$query->andWhere([$this->tableName().'.payment_date' => NULL]);

            $query->andWhere([
                'OR',
                [$this->tableName().'.payment_date' => NULL],
                [$this->tableName().'.payment_date' => 0],
                [$this->tableName().'.payment_method_id' => 0],
                [$this->tableName().'.payment_method_id' => NULL],
                [$this->tableName().'.transport_expenses_paymenter_id' => 0],
                [$this->tableName().'.transport_expenses_paymenter_id' => NULL],
            ]);
        }
//        else if($this->is_payed == 'accepted')  {
//            $query->andFilterWhere(['>', $this->tableName().'.expenses_is_taken', 0]);
//        }else if($this->is_payed == 'not_accepted')  {
//            $query->andWhere([
//                'OR',
//                [$this->tableName().'.expenses_is_taken' => NULL],
//                [$this->tableName().'.expenses_is_taken' => 0],
//            ]);
//        }


//        echo "sql=".$query->createCommand()->getSql();
//        exit;


        return $dataProvider;
    }
}
