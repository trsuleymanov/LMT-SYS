<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderReport;

/**
 * OrderReportSearch represents the model behind the search form about `app\models\OrderReport`.
 */
class OrderReportSearch extends OrderReport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['id', 'day_report_trip_transport_id', 'date_sended', 'order_id', 'client_id', 'direction_id',
                'street_id_from', 'point_id_from', 'street_id_to', 'point_id_to',
                'informer_office_id', 'is_not_places', 'places_count', 'student_count', 'child_count',
                'bag_count', 'suitcase_count', 'oversized_count', 'prize_trip_count', 'time_sat',
                'use_fix_price', 'time_confirm',
                // 'time_vpz',
                'is_confirmed', 'first_writedown_click_time',
                'first_writedown_clicker_id', 'first_confirm_click_time', 'first_confirm_clicker_id',
                'radio_confirm_now', 'radio_group_1', 'radio_group_2', 'radio_group_3',
                'confirm_selected_transport', 'fact_trip_transport_driver_id',
                'has_penalty', 'relation_order_id',
                'yandex_point_from_id', 'yandex_point_to_id'
            ], 'integer'],

            [['client_name', 'direction_name', 'street_from_name', 'point_from_name', 'time_air_train_arrival',
                'street_to_name', 'point_to_name', 'time_air_train_departure', 'trip_name',
                'informer_office_name', 'comment', 'additional_phone_1', 'additional_phone_2',
                'additional_phone_3', 'first_writedown_clicker_name', 'first_confirm_clicker_name',
                'fact_trip_transport_car_reg', 'fact_trip_transport_color', 'fact_trip_transport_model',
                'fact_trip_transport_driver_fio',
                'yandex_point_from_name', 'yandex_point_to_name'
            ], 'safe'],

            [['price'], 'number'],

            [['date', 'fact_trip_transport_id', 'trip_id',
                'yandex_point_from_lat', 'yandex_point_from_long', 'yandex_point_to_lat', 'yandex_point_to_long'], 'safe']
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
        $query = OrderReport::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'day_report_trip_transport_id' => $this->day_report_trip_transport_id,
            'date_sended' => $this->date_sended,
            'order_id' => $this->order_id,
            'client_id' => $this->client_id,
            'date' => $this->date,
            'direction_id' => $this->direction_id,

            'street_id_from' => $this->street_id_from,
            'point_id_from' => $this->point_id_from,
            'street_id_to' => $this->street_id_to,
            'point_id_to' => $this->point_id_to,

            'yandex_point_from_id' => $this->yandex_point_from_id,
            'yandex_point_to_id' => $this->yandex_point_to_id,
            'yandex_point_from_lat' => $this->yandex_point_from_lat,
            'yandex_point_to_lat' => $this->yandex_point_to_lat,
            'yandex_point_from_long' => $this->yandex_point_from_long,
            'yandex_point_to_long' => $this->yandex_point_to_long,

            'trip_id' => $this->trip_id,
            'informer_office_id' => $this->informer_office_id,
            'is_not_places' => $this->is_not_places,
            'places_count' => $this->places_count,
            'student_count' => $this->student_count,
            'child_count' => $this->child_count,
            'bag_count' => $this->bag_count,
            'suitcase_count' => $this->suitcase_count,
            'oversized_count' => $this->oversized_count,
            'prize_trip_count' => $this->prize_trip_count,
            'time_sat' => $this->time_sat,
            'use_fix_price' => $this->use_fix_price,
            'price' => $this->price,
            'time_confirm' => $this->time_confirm,
            //'time_vpz' => $this->time_vpz, //- это поле first_writedown_click_time
            'is_confirmed' => $this->is_confirmed,
            'first_writedown_click_time' => $this->first_writedown_click_time,
            'first_writedown_clicker_id' => $this->first_writedown_clicker_id,
            'first_confirm_click_time' => $this->first_confirm_click_time,
            'first_confirm_clicker_id' => $this->first_confirm_clicker_id,
            'radio_confirm_now' => $this->radio_confirm_now,
            'radio_group_1' => $this->radio_group_1,
            'radio_group_2' => $this->radio_group_2,
            'radio_group_3' => $this->radio_group_3,
            'confirm_selected_transport' => $this->confirm_selected_transport,
            'fact_trip_transport_id' => $this->fact_trip_transport_id,
            'fact_trip_transport_driver_id' => $this->fact_trip_transport_driver_id,
            'has_penalty' => $this->has_penalty,
            'relation_order_id' => $this->relation_order_id,
        ]);

        $query->andFilterWhere(['like', 'client_name', $this->client_name])
            ->andFilterWhere(['like', 'direction_name', $this->direction_name])

            ->andFilterWhere(['like', 'street_from_name', $this->street_from_name])
            ->andFilterWhere(['like', 'point_from_name', $this->point_from_name])
            ->andFilterWhere(['like', 'yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', 'yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', 'time_air_train_arrival', $this->time_air_train_arrival])
            ->andFilterWhere(['like', 'street_to_name', $this->street_to_name])
            ->andFilterWhere(['like', 'point_to_name', $this->point_to_name])
            ->andFilterWhere(['like', 'time_air_train_departure', $this->time_air_train_departure])
            ->andFilterWhere(['like', 'trip_name', $this->trip_name])
            ->andFilterWhere(['like', 'informer_office_name', $this->informer_office_name])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', 'additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', 'additional_phone_3', $this->additional_phone_3])
            ->andFilterWhere(['like', 'first_writedown_clicker_name', $this->first_writedown_clicker_name])
            ->andFilterWhere(['like', 'first_confirm_clicker_name', $this->first_confirm_clicker_name])
            ->andFilterWhere(['like', 'fact_trip_transport_car_reg', $this->fact_trip_transport_car_reg])
            ->andFilterWhere(['like', 'fact_trip_transport_color', $this->fact_trip_transport_color])
            ->andFilterWhere(['like', 'fact_trip_transport_model', $this->fact_trip_transport_model])
            ->andFilterWhere(['like', 'fact_trip_transport_driver_fio', $this->fact_trip_transport_driver_fio]);

        return $dataProvider;
    }


    public function searchInformerOffice($params)
    {
        $query = OrderReport::find()
            ->leftJoin('trip_transport', '`trip_transport`.`id` = `order_report`.`fact_trip_transport_id`')
            ->leftJoin('trip', '`trip`.`id` = `order_report`.`trip_id`');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 50,
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);
        $query->andFilterWhere(['>', 'informer_office_id', 0]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'day_report_trip_transport_id' => $this->day_report_trip_transport_id,
            'date_sended' => $this->date_sended,
            'order_id' => $this->order_id,
            'client_id' => $this->client_id,
            //'date' => $this->date,
            'direction_id' => $this->direction_id,


            'street_id_from' => $this->street_id_from,
            'point_id_from' => $this->point_id_from,
            'street_id_to' => $this->street_id_to,
            'point_id_to' => $this->point_id_to,

            'yandex_point_from_id' => $this->yandex_point_from_id,
            'yandex_point_to_id' => $this->yandex_point_to_id,
            'yandex_point_from_lat' => $this->yandex_point_from_lat,
            'yandex_point_to_lat' => $this->yandex_point_to_lat,
            'yandex_point_from_long' => $this->yandex_point_from_long,
            'yandex_point_to_long' => $this->yandex_point_to_long,

            'informer_office_id' => $this->informer_office_id,
            'is_not_places' => $this->is_not_places,
            'places_count' => $this->places_count,
            'student_count' => $this->student_count,
            'child_count' => $this->child_count,
            'bag_count' => $this->bag_count,
            'suitcase_count' => $this->suitcase_count,
            'oversized_count' => $this->oversized_count,
            'prize_trip_count' => $this->prize_trip_count,
            'time_sat' => $this->time_sat,
            'use_fix_price' => $this->use_fix_price,
            'price' => $this->price,
            'time_confirm' => $this->time_confirm,
            // 'time_vpz' => $this->time_vpz, - это поле first_writedown_click_time
            'is_confirmed' => $this->is_confirmed,
            'first_writedown_click_time' => $this->first_writedown_click_time,
            'first_writedown_clicker_id' => $this->first_writedown_clicker_id,
            'first_confirm_click_time' => $this->first_confirm_click_time,
            'first_confirm_clicker_id' => $this->first_confirm_clicker_id,
            'radio_confirm_now' => $this->radio_confirm_now,
            'radio_group_1' => $this->radio_group_1,
            'radio_group_2' => $this->radio_group_2,
            'radio_group_3' => $this->radio_group_3,
            'confirm_selected_transport' => $this->confirm_selected_transport,
            'trip_transport.transport_id' => $this->fact_trip_transport_id,
            'fact_trip_transport_driver_id' => $this->fact_trip_transport_driver_id,
            'has_penalty' => $this->has_penalty,
            'relation_order_id' => $this->relation_order_id,
        ]);

        $query
            ->andFilterWhere(['like', 'trip.name', $this->trip_id])
            ->andFilterWhere(['like', 'client_name', $this->client_name])
            ->andFilterWhere(['like', 'direction_name', $this->direction_name])
            ->andFilterWhere(['like', 'time_air_train_arrival', $this->time_air_train_arrival])

            ->andFilterWhere(['like', 'street_to_name', $this->street_to_name])
            ->andFilterWhere(['like', 'point_to_name', $this->point_to_name])
            ->andFilterWhere(['like', 'yandex_point_from_name', $this->yandex_point_from_name])
            ->andFilterWhere(['like', 'yandex_point_to_name', $this->yandex_point_to_name])

            ->andFilterWhere(['like', 'time_air_train_departure', $this->time_air_train_departure])
            ->andFilterWhere(['like', 'trip_name', $this->trip_name])
            ->andFilterWhere(['like', 'informer_office_name', $this->informer_office_name])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', 'additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', 'additional_phone_3', $this->additional_phone_3])
            ->andFilterWhere(['like', 'first_writedown_clicker_name', $this->first_writedown_clicker_name])
            ->andFilterWhere(['like', 'first_confirm_clicker_name', $this->first_confirm_clicker_name])
            ->andFilterWhere(['like', 'fact_trip_transport_car_reg', $this->fact_trip_transport_car_reg])
            ->andFilterWhere(['like', 'fact_trip_transport_color', $this->fact_trip_transport_color])
            ->andFilterWhere(['like', 'fact_trip_transport_model', $this->fact_trip_transport_model])
            ->andFilterWhere(['like', 'fact_trip_transport_driver_fio', $this->fact_trip_transport_driver_fio]);

        if (!is_null($this->date) && strpos($this->date, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date', strtotime($dateStart), strtotime($dateEnd)
            ]);
        }

        if (!empty($this->street_from_name)) {
            $query->andFilterWhere([
                'OR',
                ['like', 'street_from_name', $this->street_from_name],
                ['like', 'point_from_name', $this->street_from_name]
            ]);
        }


        return $dataProvider;
    }
}
