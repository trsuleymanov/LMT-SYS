<?php

namespace app\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use app\models\Client;
use app\models\Trip;
use app\models\Transport;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
    public $client_name_phone;
    public $plan_transport_id;
    public $fact_transport_id;
    public $client_name;
    public $penalty;
    public $places_student_child_count;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'direction_id', 'cancellation_reason_id', 'confirm_selected_transport', 'fact_trip_transport_id',
                'street_id_from', 'street_id_to',
                'point_id_from', 'point_id_to', 'time_air_train_arrival', 'time_air_train_departure', 'prize_trip_count',
                'places_count', 'student_count', 'child_count', 'bag_count', 'suitcase_count', 'oversized_count',
                'is_not_places', 'informer_office_id', 'yandex_point_from_id', 'yandex_point_to_id',
                'yandex_point_from_lat', 'yandex_point_to_lat', 'yandex_point_from_long', 'yandex_point_to_long',
                'litebox_uuid', 'litebox_fn_number', 'litebox_fiscal_document_number', 'litebox_fiscal_document_attribute'
            ], 'integer'],
            [['price', 'paid_summ', 'paid_time', 'accrual_cash_back', 'used_cash_back', 'penalty_cash_back', 'cash_received_time'], 'number'],
            [['comment', 'additional_phone_1', 'additional_phone_2', 'additional_phone_3',
                'date', 'time_confirm', 'time_confirm_diff', 'time_confirm_delta', 'is_confirmed', 'time_sat', 'confirmed_time_sat', 'created_at',
                'updated_at', 'client_id', 'trip_id',
                // 'time_vpz',
                'client_name_phone', 'client_name', 'fact_transport_id', 'plan_transport_id',
                'penalty', 'places_student_child_count', 'yandex_point_from_name', 'yandex_point_to_name',
                'first_writedown_click_time', 'status_id'], 'safe'],
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


    public function search($params)
    {
        if(isset($params['OrderSearch']['date']) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $params['OrderSearch']['date'])) {
            $params['OrderSearch']['date'] = strtotime($params['OrderSearch']['date']);
        }

        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`')
            ->leftJoin('trip_transport fact_tr', 'fact_tr.`id` = `order`.`fact_trip_transport_id`')
            ->leftJoin('transport f_transport', 'f_transport.`id` = fact_tr.`transport_id`')
            ->andWhere(['>', '`order`.status_id', 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                //'time_vpz' => SORT_ASC,
                'first_writedown_click_time'  => SORT_ASC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
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
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            $this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            'f_transport.id' => $this->plan_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
            Client::tableName().'.name' => $this->client_name,
        ]);


        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            ->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

//        if (!empty($this->date)) {
//            $query->andFilterWhere(['<', $this->tableName().'.date', $this->date + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.date', $this->date]);
//        }


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
//        if (!empty($this->time_confirm)) {
//            $time_confirm = strtotime($this->time_confirm);
//            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
//            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
//        }
//        if (!empty($this->time_sat)) {
//            $time_sat = strtotime($this->time_sat);
//            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
//            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
//        }

        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        if (!is_null($this->date) && strpos($this->date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
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
        if (!is_null($this->time_confirm) && strpos($this->time_confirm, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_confirm);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_confirm', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->time_sat) && strpos($this->time_sat, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_sat);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_sat', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        return $dataProvider;
    }


    public function abnormalSearch($params) {

        if(isset($params['OrderSearch']['date']) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $params['OrderSearch']['date'])) {
            $params['OrderSearch']['date'] = strtotime($params['OrderSearch']['date']);
        }

        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`')
            ->leftJoin('trip_transport fact_tr', 'fact_tr.`id` = `order`.`fact_trip_transport_id`')
            ->leftJoin('transport f_transport', 'f_transport.`id` = fact_tr.`transport_id`')
            ->andWhere([
                'OR',
                ['`order`.status_id' => 0],
                ['`order`.status_id' => NULL],
                ['`order`.trip_id' => 0],
                ['`order`.trip_id' => NULL]
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                //'time_vpz' => SORT_ASC,
                'first_writedown_click_time'  => SORT_ASC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
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
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            $this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            'f_transport.id' => $this->plan_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
            Client::tableName().'.name' => $this->client_name,
        ]);


        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if($this->trip_id == '*') {
             //$query->andWhere(['not', [$this->tableName().'.trip_id' => null]]);
            $query->andWhere(['>', $this->tableName().'.trip_id', 0]);
        }else {
            $query->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id]);
        }


//        if (!empty($this->date)) {
//            $query->andFilterWhere(['<', $this->tableName().'.date', $this->date + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.date', $this->date]);
//        }
//        if (!empty($this->created_at)) {
//            $created_at = strtotime($this->created_at);
//            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
//        }
        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode(' - ', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


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

        if (!is_null($this->date) && strpos($this->date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }



//        if (!empty($this->time_confirm)) {
//            $time_confirm = strtotime($this->time_confirm);
//            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
//            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
//        }
//        if (!empty($this->time_sat)) {
//            $time_sat = strtotime($this->time_sat);
//            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
//            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
//        }
        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        if (!is_null($this->time_confirm) && strpos($this->time_confirm, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_confirm);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_confirm', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->time_sat) && strpos($this->time_sat, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_sat);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_sat', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }


    public function electronicRequestSearch($params) {

        if(isset($params['OrderSearch']['date']) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $params['OrderSearch']['date'])) {
            $params['OrderSearch']['date'] = strtotime($params['OrderSearch']['date']);
        }

        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`');
            //->leftJoin('trip_transport fact_tr', 'fact_tr.`id` = `order`.`fact_trip_transport_id`')
            //->leftJoin('transport f_transport', 'f_transport.`id` = fact_tr.`transport_id`')
            //->leftJoin('dispatcher_accounting', 'dispatcher_accounting.`order_id` = order.`id`')
//            ->andWhere([
//                'OR',
//                ['`order`.status_id' => 0],
//                ['`order`.status_id' => NULL],
//                ['`order`.trip_id' => 0],
//                ['`order`.trip_id' => NULL]
//            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'first_writedown_click_time'  => SORT_ASC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
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
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            //$this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            //'f_transport.id' => $this->plan_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
            Client::tableName().'.name' => $this->client_name,
        ]);


        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3])
            ->andFilterWhere(['like', Client::tableName().'.mobile_phone', $this->client_name_phone]);

        if($this->trip_id == '*') {
            //$query->andWhere(['not', [$this->tableName().'.trip_id' => null]]);
            $query->andWhere(['>', $this->tableName().'.trip_id', 0]);
        }else {
            $query->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id]);
        }


        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode(' - ', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }



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

        if (!is_null($this->date) && strpos($this->date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }



        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        if (!is_null($this->time_confirm) && strpos($this->time_confirm, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_confirm);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_confirm', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }
        if (!is_null($this->time_sat) && strpos($this->time_sat, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->time_sat);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.time_sat', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        //external_type - 'client_server_client_ext','client_server_request'
        $query->andFilterWhere([
            $this->tableName().'.external_type' => "client_site",
            //$this->tableName().'.status_id' => 0,
        ]);


        return $dataProvider;
    }

    public function cancellationSearch($params) {

        if(isset($params['OrderSearch']['date']) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $params['OrderSearch']['date'])) {
            $params['OrderSearch']['date'] = strtotime($params['OrderSearch']['date']);
        }

        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`')
            ->leftJoin('trip_transport fact_tr', 'fact_tr.`id` = `order`.`fact_trip_transport_id`')
            ->leftJoin('transport f_transport', 'f_transport.`id` = fact_tr.`transport_id`')
            ->andWhere(['>', '`order`.status_id', 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => Yii::$app->session->get('table-rows', 20)
//            ],
            'pagination' => false
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                //'time_vpz' => SORT_ASC,
                'first_writedown_click_time'  => SORT_ASC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
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
            $this->tableName().'.status_id' => $this->status_id,
            //$this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            $this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            'f_transport.id' => $this->plan_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
            //Client::tableName().'.name' => $this->client_name,
        ]);
        if($this->cancellation_reason_id > 0) {
            $query->andFilterWhere([$this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id]);
        }else {
            $query->andFilterWhere(['>', $this->tableName().'.cancellation_reason_id', 0]);
        }

        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_name])
            ->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if (!empty($this->date)) {
            $query->andFilterWhere(['<', $this->tableName().'.date', $this->date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $this->date]);
        }
        if (!empty($this->created_at)) {
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
        }
        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode(' - ', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        return $dataProvider;
    }



    public function searchDayPrint($params)
    {
        // Выбираются заказы только за определенный день и кроме удаленных
        if(isset($params['OrderSearch']['date']) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $params['OrderSearch']['date'])) {
            $params['OrderSearch']['date'] = strtotime($params['OrderSearch']['date']);
        }

        $order_status_deleted = OrderStatus::getByCode('canceled');


        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`')
            ->leftJoin('trip_transport fact_tr', 'fact_tr.`id` = `order`.`fact_trip_transport_id`')
            ->leftJoin('transport f_transport', 'f_transport.`id` = fact_tr.`transport_id`')
            ->andWhere(['>', '`order`.status_id', 0])
            ->andWhere(['!=', $this->tableName().'.status_id', $order_status_deleted->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 500, // 500 - нужно для таблицы Заказов в админке
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'direction_id' => SORT_ASC,
                // 'time_vpz' => SORT_ASC,
                'first_writedown_click_time'  => SORT_ASC,
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
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
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            $this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            'f_transport.id' => $this->plan_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
            Client::tableName().'.name' => $this->client_name,
        ]);


        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            ->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if (!empty($this->date)) {
            $query->andFilterWhere(['<', $this->tableName().'.date', $this->date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $this->date]);
        }
        if (!empty($this->created_at)) {
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
        }
        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode(' - ', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        return $dataProvider;
    }


    public function clientSearch($client_id, $params, $type_search = '')
    {
        //$query = Order::find()->joinWith(['client', 'trip']);
        if(!empty($type_search)) {
            $delimiter_data = "01.01." . date("Y");
            $unixtime_delimiter_data = strtotime($delimiter_data);
        }

        $query = Order::find()
            ->leftJoin('client', '`client`.`id` = `order`.`client_id`')
            ->leftJoin('trip', '`trip`.`id` = `order`.`trip_id`')
            ->leftJoin('trip_transport', '`trip_transport`.`id` = `order`.`fact_trip_transport_id`')
            ->leftJoin('transport', '`transport`.`id` = `trip_transport`.`transport_id`')
            ->andWhere(['>', '`order`.status_id', 0]);

        if($type_search == 'past_years') {
            $query->andWhere(['<', '`order`.date', $unixtime_delimiter_data]);
        }elseif($type_search == 'current_year') {
            $query->andWhere(['>=', '`order`.date', $unixtime_delimiter_data]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['date' => SORT_DESC],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'fact_transport_id' => [
                    'asc' => [Transport::tableName().'.model' => SORT_ASC],
                    'desc' => [Transport::tableName().'.model' => SORT_DESC]
                ],
            ])
        ]);
        $query->andFilterWhere([
            'client_id' => $client_id
        ]);


        $this->load($params);

        if (!$this->validate()) { // дата из формата 'dd.mm.yyyy' преобразовалась в unixtime!
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            //$this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            //$this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,
            TripTransport::tableName().'.transport_id' => $this->fact_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            //$this->tableName().'.baggage' => $this->baggage,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
        ]);


        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            //->andFilterWhere(['like', Client::tableName().'.name', $this->client_name])
            //->andFilterWhere(['like', Client::tableName().'.mobile_phone', $this->client_phone])
            ->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);


        if (!empty($this->status_id)) {
            if($this->status_id == 1 || $this->status_id == 2) {
                $query->andFilterWhere(['=', $this->tableName().'.status_id', $this->status_id]);
            }elseif($this->status_id == 3) {
                $query->andFilterWhere(['=', $this->tableName().'.status_id', 3]);
                $query->andWhere([Trip::tableName().'.date_sended' => null]);

            }elseif($this->status_id == 4) {
                $query->andFilterWhere(['=', $this->tableName().'.status_id', 3]);
                $query->andWhere(['not', [Trip::tableName().'.date_sended' => null]]);
            }
        }

        if (!empty($this->date)) {
            //$unixdate = strtotime($this->date);
            $query->andFilterWhere(['<', $this->tableName().'.date',$this->date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $this->date]);
            $this->date = date('d.m.Y', $this->date);
        }

//        if (!empty($this->created_at)) {
//            $created_at = strtotime($this->created_at);
//            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
//            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
//        }
        if (!empty($this->first_writedown_click_time)) {
            $first_writedown_click_time = strtotime($this->first_writedown_click_time);
            $query->andFilterWhere(['<', $this->tableName().'.first_writedown_click_time', $first_writedown_click_time + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.first_writedown_click_time', $first_writedown_click_time]);
        }

        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        return $dataProvider;
    }

    /**
     * Поиск для таблицы в админке
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function TripSearch($params, $trip_id)
    {
        $query = Order::find()
            ->joinWith(['client', 'trip'])
            ->andWhere([
                'OR',
                ['>', '`order`.status_id', 0],
                [
                    'AND',
                    ['`order`.status_id' => 0],
                    //['>', '`order`.client_server_ext_id', 0]
                    ['>', '`order`.external_id', 0],
                    //['`order`.external_type' => "client_site"]
                ]
            ]);
            //->andWhere(new Expression('((`order`.status_id>0) OR (`order`.status_id=0 AND `order`.client_server_ext_id > 0))'));
            //->andWhere(['>', '`order`.status_id', 0]);

        // add conditions that should always apply here

        $dataProvider = new OrderSearchProvider([
            'query' => $query,
            'pagination' => false
        ]);

        if(isset($params['empty_rows_count'])) {
            $dataProvider->empty_rows_count = intval($params['empty_rows_count']);
        }

        $dataProvider->setSort([
            'defaultOrder' => [
                'cancellation_reason_id' => SORT_ASC,
                'time_confirm' => SORT_ASC,
                'time_confirm_sort' => SORT_ASC,
                //'time_vpz' => SORT_ASC
            ],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'client_name_phone' => [
                    'asc' => [Client::tableName().'.name' => SORT_ASC],
                    'desc' => [Client::tableName().'.name' => SORT_DESC]
                ],
                'time_confirm' => [
                    'asc' => [
                        Order::tableName().'.is_confirmed' => SORT_ASC,
                        Order::tableName().'.time_confirm' => SORT_ASC
                    ],
                    'desc' => [
                        Order::tableName().'.is_confirmed' => SORT_DESC,
                        Order::tableName().'.time_confirm' => SORT_DESC
                    ]
                ],
            ])
        ]);
        $dataProvider->sort->route = '/trip/trip-orders';

        $this->load($params);
        //echo "this:<pre>"; print_r($this); echo "</pre>";

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            $this->tableName().'.trip_id' => $trip_id,

            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.cancellation_reason_id' => $this->cancellation_reason_id,

            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.confirm_selected_transport' => $this->confirm_selected_transport,
            $this->tableName().'.fact_trip_transport_id' => $this->fact_trip_transport_id,

            $this->tableName().'.street_id_from' => $this->street_id_from,
            $this->tableName().'.street_id_to' => $this->street_id_to,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.yandex_point_from_id' => $this->yandex_point_from_id,
            $this->tableName().'.yandex_point_to_id' => $this->yandex_point_to_id,
            $this->tableName().'.yandex_point_from_lat' => $this->yandex_point_from_lat,
            $this->tableName().'.yandex_point_to_lat' => $this->yandex_point_to_lat,
            $this->tableName().'.yandex_point_from_long' => $this->yandex_point_from_long,
            $this->tableName().'.yandex_point_to_long' => $this->yandex_point_to_long,

            $this->tableName().'.time_air_train_arrival' => $this->time_air_train_arrival,
            $this->tableName().'.time_air_train_departure' => $this->time_air_train_departure,

            $this->tableName().'.prize_trip_count' => $this->prize_trip_count,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.price' => $this->price,
            $this->tableName().'.paid_summ' => $this->paid_summ,
            $this->tableName().'.paid_time' => $this->paid_time,
            $this->tableName().'.accrual_cash_back' => $this->accrual_cash_back,
            $this->tableName().'.used_cash_back' => $this->used_cash_back,
            $this->tableName().'.penalty_cash_back' => $this->penalty_cash_back,
            $this->tableName().'.cash_received_time' => $this->cash_received_time,
            $this->tableName().'.is_confirmed' => $this->is_confirmed,
        ]);

        $query
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', $this->tableName().'.yandex_point_to_name', $this->yandex_point_to_name])
            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_name_phone])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if (!empty($this->date)) {
            $date = strtotime($this->date);
            $query->andFilterWhere(['<', $this->tableName().'.date', $date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $date]);
        }
        if (!is_null($this->first_writedown_click_time) && strpos($this->first_writedown_click_time, '-') !== false) {
            list($dateStart, $dateEnd) = explode(' - ', $this->first_writedown_click_time);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName().'.first_writedown_click_time', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }


        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        if (!empty($this->confirmed_time_sat)) {
            $confirmed_time_sat = strtotime($this->confirmed_time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.confirmed_time_sat', $confirmed_time_sat]);
        }

        return $dataProvider;
    }


    public function getLastOrdersSearch($client_id) {

        // нужно получить:
        // последний отправленный заказ клиента
        // все заказы которые не отправлены с датой заказа сегодня и больше
//        $sent_order_status = OrderStatus::getByCode('sent');
//        $last_sended_order = Order::find()
//            ->where(['client_id' => $client_id])
//            ->andWhere(['status_id' => $sent_order_status->id])
//            ->orderBy(['id' => SORT_DESC])
//            ->one();
//        $last_sended_order_id = ($last_sended_order != null ? $last_sended_order->id : 0);
        $new_orders = Order::find()
            ->where(['client_id' => $client_id])
            ->andWhere(['>=', 'date', strtotime(date('d.m.Y'))])
            //->andWhere(['!=', 'id', $last_sended_order_id])
            ->andWhere(['>', 'status_id', 0])
            ->all();

        //echo "client_id=$client_id last_sended_order_id=$last_sended_order_id date=".strtotime(date('d.m.Y'))."<br />";
        $orders = [];
//        if($last_sended_order != null) {
//            $orders[] = $last_sended_order;
//        }
        foreach($new_orders as $new_order) {
            $orders[] = $new_order;
        }

        //echo "orders:<pre>"; print_r($orders); echo "</pre>"; exit;

        $dataProvider = new OrderTestProvider([
            'models' => $orders,
        ]);

        return $dataProvider;
    }


    /*
     * Поиск всех заказов неотправленных в одном из полей которого есть телефон $phone
     */
    public function getSearchOrdersByPhone($phone) {

        $clients = Client::find()->where([
            'OR',
            //['mobile_phone' => $phone],
            ['home_phone' => $phone],
            ['alt_phone' => $phone],
        ])->all();

        //echo "clients:<pre>"; print_r($clients); echo "</pre>"; exit();


        //echo "client_id=$client_id last_sended_order_id=$last_sended_order_id date=".strtotime(date('d.m.Y'))."<br />";
        $orders = [];
        $clients_orders = [];
        if(count($clients) > 0) {
            $clients_orders = Order::find()
                ->where(['client_id' => ArrayHelper::map($clients, 'id', 'id')])
                ->andWhere(['>=', 'date', strtotime(date('d.m.Y'))])
                ->andWhere(['>', 'status_id', 0])
                ->all();
        }

        $additional_phone_orders = Order::find()
            ->where(['>=', 'date', strtotime(date('d.m.Y'))])
            ->andWhere(['>', 'status_id', 0])
            ->andWhere([
                'OR',
                ['additional_phone_1' => $phone],
                ['additional_phone_2' => $phone],
                ['additional_phone_3' => $phone]
            ])
            ->all();

        if(count($clients_orders) > 0) {
            foreach($clients_orders as $client_order) {
                $orders[] = $client_order;
            }
        }
        if(count($additional_phone_orders) > 0) {
            foreach($additional_phone_orders as $additional_phone_order) {
                $orders[] = $additional_phone_order;
            }
        }

        $dataProvider = new OrderTestProvider([
            'models' => $orders,
        ]);

        return $dataProvider;
    }

}


class OrderTestProvider extends \yii\data\ActiveDataProvider {

    //public $empty_rows_count = 0;
    public $models = [];

    public function getModels() {

//        $models = parent::getModels();
//
//        for($i = 0; $i < $this->empty_rows_count; $i++) {
//            $models[] = new OrderSearch();
//        }
//
//        return $models;

        return $this->models;
    }

    public function getKeys()
    {
        //$keys =  parent::getKeys(); // TODO: Change the autogenerated stub

//        for($i = 0; $i < $this->empty_rows_count; $i++) {
//            $keys[] = 0;
//        }

        //return $keys;

        return array_keys($this->models);
    }

    public function getTotalCount()
    {
        //return parent::getTotalCount(); // TODO: Change the autogenerated stub
        return count($this->models);
    }


}