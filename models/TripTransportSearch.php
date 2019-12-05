<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TripTransport;
use app\models\Trip;
use app\models\Direction;
use app\models\Transport;
use app\models\Driver;

/**
 * TripTransportSearch represents the model behind the search form about `app\models\TripTransport`.
 */
class TripTransportSearch extends TripTransport
{
    public $driver_fio;
    public $transport_name;
    public $transport_places_count;
    public $orders_places_count;
    public $orders_student_count;
    public $orders_child_count;
    public $orders_prize_trip_count;
    public $orders_airport_count;
    public $orders_use_fix_price_count;
    public $orders_is_not_places_count;
    public $orders_price;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'transport_id', 'driver_id', 'trip_id'], 'integer'],
            [['direction_id', 'date_sended', 'driver_fio', 'transport_name',
            'transport_places_count',
                'orders_places_count', 'orders_student_count', 'orders_child_count',
            'orders_prize_trip_count', 'orders_airport_count', 'orders_use_fix_price_count',
            'orders_is_not_places_count', 'orders_price'], 'safe']
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
    public function search($params, $trip_id = 0)
    {
        $query = TripTransport::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->setSort([
            'defaultOrder' => [
                'sort' => SORT_DESC,
            ],
        ]);
        $dataProvider->sort->route = '/trip/trip-orders';

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions

        $query->andFilterWhere([
            'id' => $this->id,
            'transport_id' => $this->transport_id,
            'driver_id' => $this->driver_id,
            'trip_id' => $trip_id > 0 ? $trip_id : $this->trip_id,
        ]);

        return $dataProvider;
    }

    public function searchDriverAccounting($params) {

        $query = TripTransport::find()
            ->leftJoin('driver', '`driver`.`id` = `trip_transport`.`driver_id`')
            ->leftJoin('transport', '`transport`.`id` = `trip_transport`.`transport_id`')
            ->leftJoin('order', '`order`.`fact_trip_transport_id` = `trip_transport`.`id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                //'pageSize' => 100,
                'pageSize' => Yii::$app->session->get('table-rows', 20)
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['date_sended' => SORT_DESC],
            'attributes' => array_merge($dataProvider->getSort()->attributes, [
                'driver_fio' => [
                    'asc' => [Driver::tableName().'.fio' => SORT_ASC],
                    'desc' => [Driver::tableName().'.fio' => SORT_DESC]
                ],
                'transport_name' => [
                    'asc' => [Transport::tableName().'.model' => SORT_ASC],
                    'desc' => [Transport::tableName().'.model' => SORT_DESC]
                ],
                'transport_places_count' => [
                    'asc' => [Transport::tableName().'.places_count' => SORT_ASC],
                    'desc' => [Transport::tableName().'.places_count' => SORT_DESC]
                ],
            ])
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            TripTransport::tableName().'.status_id' => 1, // Смотрим только отправленные поездки
            TripTransport::tableName().'.id' => $this->id,
            TripTransport::tableName().'.transport_id' => $this->transport_id,
            Transport::tableName().'.places_count' => $this->transport_places_count,
            TripTransport::tableName().'.driver_id' => $this->driver_id,
        ]);

        if (!is_null($this->date_sended) && strpos($this->date_sended, '-') !== false) {
            list($dateStart, $dateEnd) = explode('-', $this->date_sended);
            $query->andFilterWhere([
                'BETWEEN', $this->tableName() . '.date_sended', strtotime($dateStart), strtotime($dateEnd) + 3600 * 24 - 1
            ]);
        }

        return $dataProvider;
    }
}
