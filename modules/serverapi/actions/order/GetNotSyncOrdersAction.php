<?php

namespace app\modules\serverapi\actions\order;

use app\models\Client;
use app\models\Order;
use app\models\OrderStatus;
use app\models\Point;
use app\models\Street;
use app\models\Transport;
use app\models\Trip;
use app\models\TripTransport;
use yii\helpers\ArrayHelper;
use Yii;


class GetNotSyncOrdersAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращает данные по всем заказам у которых дата создания-обновления меньше даты синхронизации.
     *
     * запрос: curl -i -H "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/get-not-sync-orders
     * запрос с кодом доступа: curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-yii2.ru/serverapi/order/get-not-sync-orders
     */
    public function run()
    {
        \Yii::$app->response->format = 'json';


        //    Статусы заказа:
//        $aStatuses = [
//            1 => 'created', // создан
//            2 => 'pending_send', // в ожидании отправления
//            3 => 'sended', // отправлен
//            4 => 'canceled', // отменен
//        ];

    //    + на клиенском сервере нужно создать таблицу order, в которой будут поля: - теперь это не нужно!
    //    - id
    //    - user_id
    //    - client_ext_id
    //    - main_server_order_id
    //    - data_mktime
    //    - direction - строка varchar(50)
    //    - trip_name - varchar(50)
    //    - trip_date
    //    - status - small int  - в моделе массив статусов
    //    - street_from - varchar(50)
    //    - point_from - varchar(50)
    //    - street_to
    //    - point_to
    //    - places_count - small int
    //    - transport_car_reg - varchar(20)
    //    - transport_model - varchar(50)
    //    - transport_color - varchar(50)
    //    - price
    //


        // находим все заказы у которых (дата создания или дата изменения) больше даты синхронизации
        $aOrdersData = [];

        $sql = 'SELECT * FROM `order` WHERE
          ((`sync_date` IS NULL) OR (`sync_date` < `created_at`) OR (`sync_date` < `updated_at`))
          AND status_id > 0 AND client_id > 0
          LIMIT 100';
        $orders = Yii::$app->db->createCommand($sql)->queryAll();


        $aTripsId = []; $aTrips = [];
        $aTripsTransports = []; $aTripsTransportsModels = [];

        $aClientsId = [];
        foreach($orders as $order) {
            $aTripsId[$order['trip_id']] = $order['trip_id'];
            $aClientsId[$order['client_id']] = $order['client_id'];

            if(!empty($order['fact_trip_transport_id'])) {
                $aTripsTransports[$order['fact_trip_transport_id']] = $order['fact_trip_transport_id'];
            }
        }

        $aClients = [];
        if(count($aClientsId) > 0) {
            $rows = (new \yii\db\Query())
                ->select(['id', 'email', 'name', 'mobile_phone', 'cashback'])
                ->from(Client::tableName())
                ->where(['id' => $aClientsId])
                ->all();
            foreach($rows as $row) {
                $aClients[$row['id']] = $row;
            }
        }

        if(count($aTripsId) > 0) {
            $rows = (new \yii\db\Query())
                ->select(['id', 'name', 'mid_time', 'date'])
                ->from(Trip::tableName())
                ->where(['id' => $aTripsId])
                ->all();
            foreach($rows as $row) {
                $aTrips[$row['id']] = $row;
            }
        }


        if(count($aTripsTransports) > 0) {
            $rows = (new \yii\db\Query())
                ->select(['id', 'transport_id'])
                ->from(TripTransport::tableName())
                ->where(['id' => $aTripsTransports])
                ->all();

            $aTransports = [];
            foreach($rows as $row) {
                $aTripsTransports[$row['id']] = $row['transport_id'];
                $aTransports[$row['transport_id']] = $row['transport_id'];
            }

            $transports = Transport::find()->where(['id' => $aTransports])->all();
            $aTransports = ArrayHelper::index($transports, 'id');
            foreach($rows as $row) {
                $aTripsTransportsModels[$row['id']] = $aTransports[$row['transport_id']];
            }
        }

        $order_statuses = OrderStatus::find()->all();
        $aOrderStatuses = ArrayHelper::map($order_statuses, 'id', 'code');

        foreach($orders as $order) {

            //    Статусы заказа на сайте:
            //    - создан - 0 - created
            //    - отправлен - sended
            //    - отменен - canceled

            //    Статусы заявки в приложении:
//            'created' => 'ЗАКАЗ ЗАРЕГИСТРИРОВАН',   ~ заявка есть, заказа нет
//            'pending_call' => 'ЗАКАЗ ОБРАБОТАН, ЖДИТЕ ЗВОНКА',  ~ order.[status=created] + order.time_confirm = NULL
//            'pending_send' => 'НАЗНАЧЕНО ВРЕМЯ', // ОЖИДАЕТ ОТПРАВКИ ~ order.[status=created] + order.time_confirm > 0
//            'sended' => 'ОТПРАВЛЕН',   ~ order.[status=sended]
//            'canceled' => 'ОТМЕНЕН'   ~ order.[status=canceled]

            $transport = (isset($aTripsTransportsModels[$order['fact_trip_transport_id']]) ? $aTripsTransportsModels[$order['fact_trip_transport_id']]: null);
            $aClient = $aClients[$order['client_id']];

            $aOrdersData[] = [
                'order_id' => $order['id'],
                //'client_server_ext_id' => $order['client_server_ext_id'],
                'client_server_ext_id' => $order['external_id'],
                'client_server_external_type' => $order['external_type'],
                'status_code' => $aOrderStatuses[$order['status_id']],
                'status_setting_time' => $order['status_setting_time'],
                'canceled_by' => $order['canceled_by'],

                'time_confirm' => $order['time_confirm'],
                'trip_name' => (isset($aTrips[$order['trip_id']]) ? $aTrips[$order['trip_id']]['name'] : ''),
                'trip_date' => (isset($aTrips[$order['trip_id']]) ? $aTrips[$order['trip_id']]['date'] : ''),
                'trip_mid_time' => (isset($aTrips[$order['trip_id']]) ? $aTrips[$order['trip_id']]['mid_time'] : ''),
                'time_sat' => $order['time_sat'],
                // 'trip_id' => $order['trip_id'], // рейс ищется по date, direction, name

                //'street_from' => (isset($aStreetsFromNames[$order['street_id_from']]) ? $aStreetsFromNames[$order['street_id_from']] : ''),
                //'point_from' => (isset($aPointsFromNames[$order['point_id_from']]) ? $aPointsFromNames[$order['point_id_from']] : ''),
                //'street_to' => (isset($aStreetsToNames[$order['street_id_to']]) ? $aStreetsToNames[$order['street_id_to']] : ''),
                //'point_to' => (isset($aPointsToNames[$order['point_id_to']]) ? $aPointsToNames[$order['point_id_to']] : ''),
                'places_count' => $order['places_count'],
                'student_count' => $order['student_count'],
                'child_count' => $order['child_count'],
                'is_not_places' => $order['is_not_places'],
                'prize_trip_count' => $order['prize_trip_count'],
                'bag_count' => $order['bag_count'],
                'suitcase_count' => $order['suitcase_count'],

                'yandex_point_from_id' => $order['yandex_point_from_id'],
                'yandex_point_from_name' => $order['yandex_point_from_name'],
                'yandex_point_from_lat' => $order['yandex_point_from_lat'],
                'yandex_point_from_long' => $order['yandex_point_from_long'],
                'yandex_point_to_id' => $order['yandex_point_to_id'],
                'yandex_point_to_name' => $order['yandex_point_to_name'],
                'yandex_point_to_lat' => $order['yandex_point_to_lat'],
                'yandex_point_to_long' => $order['yandex_point_to_long'],

                'price' => $order['price'],
                'paid_summ' => $order['paid_summ'],
                'is_paid' => $order['is_paid'],
                'payment_source' => $order['payment_source'],

                'accrual_cash_back' => $order['accrual_cash_back'],
                'penalty_cash_back' => $order['penalty_cash_back'],
                'used_cash_back' => $order['used_cash_back'],

                'transport_car_reg' => (!empty($transport) ? $transport->car_reg : ''),
                'transport_model' => (!empty($transport) ? $transport->model : ''),
                'transport_color' => (!empty($transport) ? $transport->color : ''),

                //'client_id' => $aClient['id'],
                'direction_name' => $order['direction_id'] == 1 ? 'АК' : 'КА',
                'date' => $order['date'],
                //'trip_time' => // используем trip_name

                'client_email' => $aClient['email'],
                'client_name' => $aClient['name'],
                'client_mobile_phone' => $aClient['mobile_phone'],
                //'client_cashback' => $aClient['cashback'],
            ];
        }


        //return $aOrdersData;
        return [];
    }
}