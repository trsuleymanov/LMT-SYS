<?php

namespace app\modules\api\actions\map;

use app\models\Driver;
use app\models\Order;
use app\models\TripTransport;
use Yii;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\models\Trip;
use app\models\OrderStatus;

class GetMapDataAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Отображение
     */
    public function run()
    {
        // 1.сервер определяет id водителя по токену
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь не найден');
        }

        $driver = Driver::find()->where(['user_id' => $user->id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $transport_access_key = Yii::$app->getRequest()->getBodyParam('transport_access_key');
        $trip_transport = TripTransport::find()->where(['access_key' => $transport_access_key])->one();
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Идентификатор недействителен');
        }

        if($trip_transport->driver_id != $driver->id) {
            throw new ForbiddenHttpException('Текущий водитель не имеет доступа к этому транспорту user_id='.$user->id.' driver_id='.$driver->id);
        }


        // 1. нахожу все! trip_transports привязанные к текущему водителю, но еще не отправленные
        // 2. нахожу все неотправленные и не отмененные заказы, у которые есть время посадки, но нет времени
        //  подтверждения посадки и привязанные к найденным trip_transports

        // сегодняшние рейсы
//        $today_trips = Trip::find()->where(['date' => strtotime(date('d.m.Y'))])->all();
//
//        // нахожу все! сегодняшние trip_transports привязанные к текущему водителю, но еще не отправленные
//        $trip_transport = TripTransport::find()
//            ->where(['IN', 'trip_id', ArrayHelper::map($today_trips, 'id', 'id')])
//            ->andWhere(['driver_id' => $driver->id])
//            ->andWhere(['status_id' => 0])
//            ->one();
//        if($trip_transport == null) {
//            throw new ForbiddenHttpException('Сегодня водитель не записан на рейсы');
//        }

        // нахожу все неотправленные и не отмененные заказы, у которые есть время посадки, но нет времени
        //  подтверждения посадки и привязанные к найденным trip_transports
//        $order_status_new = OrderStatus::getByCode('created');
//        $order_status_sent = OrderStatus::getByCode('sent');
        $order_statuses = OrderStatus::find()->where(['code' => ['created', 'sent']])->all();
        $orders = Order::find()
            ->where(['status_id' => ArrayHelper::map($order_statuses, 'id', 'id')])
            ->andWhere(['>','time_sat', 0])
            //->andWhere(['confirmed_time_sat' => NULL])
            //->andWhere(['IN', 'fact_trip_transport_id', ArrayHelper::map($trip_transports, 'id', 'id')])
            ->andWhere(['fact_trip_transport_id' => $trip_transport->id])
            ->all();

        $city_from = $trip_transport->trip->direction->cityFrom;

        $pointsOrdersData = [];
        foreach($orders as $order) {
            if(!empty($order->yandex_point_from_lat) && !empty($order->yandex_point_from_long) && !empty($order->yandex_point_from_name)) {
                $key = $order->yandex_point_from_lat.'_'.$order->yandex_point_from_long;
                if(!isset($pointsOrdersData[$key])) {
                    $pointsOrdersData[$key] = [
                        'id' => $order->yandex_point_from_id,
                        'name' => $order->yandex_point_from_name,
                        'lat' => $order->yandex_point_from_lat,
                        'long' => $order->yandex_point_from_long,
                    ];
                }

            }elseif($order->yandex_point_from_id > 0) {

                $yandex_point_from = $order->yandexPointFrom;
                $key = $yandex_point_from->lat.'_'.$yandex_point_from->long;
                if(!isset($pointsOrdersData[$key])) {
                    $pointsOrdersData[$key] = [
                        'id' => $order->yandex_point_from_id,
                        'name' => $order->yandex_point_from_name,
                        'lat' => $order->yandex_point_from_lat,
                        'long' => $order->yandex_point_from_long,
                    ];
                }
            }

            $aStatuses = [
                0 => '',
                1 => 'created',
                2 => 'canceled',
                3 => 'sent'
            ];

            $pointsOrdersData[$key]['orders'][] = [
                'id' => $order->id,
                'status' => $aStatuses[intval($order->status_id)],
                'status_setting_time' => intval($order->status_setting_time),
                'client_name' => ($order->client != null ? $order->client->name : ''),
                'client_mobile' => ($order->client != null ? $order->client->mobile_phone : ''),
                'price' => $order->price,

                'paid_summ' => $order->paid_summ,
                'payment_source' => $order->payment_source, // 'client_site', 'application', 'crm', ''
                'used_cash_back' => $order->used_cash_back,
                //'cash_received' => $order->cash_received_time > 0 ? true : false,
                'cash_received_time' => intval($order->cash_received_time),
                //'is_paid' => $order->is_paid,
                'paid_time' => intval($order->paid_time),
                'time_sat' => intval($order->time_sat),
                'confirmed_time_sat' => intval($order->confirmed_time_sat),

                'places_count' => intval($order->places_count),
                'student_count' => intval($order->student_count),
                'child_count' => intval($order->child_count),
                'suitcase_count' => intval($order->suitcase_count),
                'bag_count' => intval($order->bag_count),
                'oversized_count' => intval($order->oversized_count),
                'prize_trip_count' => intval($order->prize_trip_count),
                'yandex_point_to_name' => $order->yandex_point_to_name,
                'yandex_point_to_lat' => $order->yandex_point_to_lat,
                'yandex_point_to_long' => $order->yandex_point_to_long,
                'yandex_point_from_name' => $order->yandex_point_from_name,
                'yandex_point_from_lat' => $order->yandex_point_from_lat,
                'yandex_point_from_long' => $order->yandex_point_from_long,
                'vrpt' => date('H:i', $order->time_confirm),
                'time_confirm' => $order->time_confirm,
                'time_confirm_sort' => $order->time_confirm_sort,
                'comment' => $order->comment
            ];
        }

        // убивая ключи $key мы превращаем при получении ответа у клиента jsonОбъект в jsonМассив
        $arPointsOrdersData = [];
        foreach($pointsOrdersData as $pointOrdersData) {
            $arPointsOrdersData[] = $pointOrdersData;
        }

        //$trip = $trip_transport->trip;
        //$direction = $trip->direction;
        $transport = $trip_transport->transport;
        $driver = $trip_transport->driver;

        return [
            'count_orders' => count($orders),
            'pointsData' => $arPointsOrdersData,
            'city_from' => $city_from,
                            // АК 08.12.2017 10:00 (09:00, 09:30, 10:00)
            //'trip_name' => $direction->sh_name.' '.date('d.m.Y').' '.$trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')'
            'transport' => $transport->car_reg.' '.$transport->model,
            'driver' => ($driver != null ? $driver->fio : ''),
            'confirmed_fact_places_count' => $trip_transport->confirmFactPlacesCount,
            'fact_places_count' => $trip_transport->factPlacesCount,
            'transport_places_count' => $transport->places_count
        ];
    }
}
