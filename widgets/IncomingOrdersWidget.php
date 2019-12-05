<?php
namespace app\widgets;

use app\models\Call;
use app\models\Client;
use app\models\Order;
use app\models\OrderStatus;
use app\models\SocketDemon;
use app\models\Transport;
use app\models\Trip;
use app\models\TripTransport;
use Yii;
use yii\base\Widget;
use app\models\UserRole;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * Окно с поступившими заявками мобильными и со звонками
 */
class IncomingOrdersWidget extends Widget
{
    public $is_open = false;

    public function run()
    {
        // данные для модального окна "Свежие заявки"
        $mobile_orders = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'external_id', 0])
            ->andWhere(['external_type' => 'application'])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        // данные для окна "Электронные заявки"
        $client_orders = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'external_id', 0])
            ->andWhere(['external_type' => 'client_site'])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        if(isset($_GET['trips-modal-is-open'])) {
            $trips_modal_is_open = true;
        }else {
            $trips_modal_is_open = false;
        }

        // incoming-orders-is-open
        if(isset($_GET['incoming-clientext-orders-is-open'])) {
            $incoming_clientext_orders_is_open = true;
        }else {
            $incoming_clientext_orders_is_open = false;
        }

        if(isset($_GET['incoming-request-orders-is-open'])) {
            $incoming_request_orders_is_open = true;
        }else {
            $incoming_request_orders_is_open = false;
        }

//        $active_calls = Call::find()
//            ->where([
//                'OR',
//                ['>', 't_create', 0],
//                ['ats_eok_time' => 0],
//                ['ats_eok_time' => NULL],
//            ])->all();



        list($aActiveTrips, $direction1_has_bad_trip, $direction2_has_bad_trip) = self::getActiveTripsData();

        return $this->render('@app/widgets/views/incoming-orders/index', [
            'direction1_has_bad_trip' => $direction1_has_bad_trip,
            'direction2_has_bad_trip' => $direction2_has_bad_trip,
            'direction1_trips_count' => isset($aActiveTrips[1]) ? count($aActiveTrips[1]) : 0,
            'direction2_trips_count' => isset($aActiveTrips[2]) ? count($aActiveTrips[2]) : 0,
            'aActiveTrips' => $aActiveTrips,
            'trips_modal_is_open' => $trips_modal_is_open,

            'incoming_clientext_orders_is_open' => $incoming_clientext_orders_is_open,
            'incoming_request_orders_is_open' => $incoming_request_orders_is_open,
            //'active_calls' => $active_calls,
            'mobile_orders' => $mobile_orders,
            'client_orders' => $client_orders
        ]);
    }


    public static function getActiveTripsData() {

        $aActiveTrips = [];
        $canceled_order_status = OrderStatus::getByCode('canceled');

        // на направлении АК сегодня сколько рейсов в статусе "отправка начата"
        $direction1_start_trips = Trip::find()
            ->where(['direction_id' => 1])
            ->andWhere(['>', 'date_start_sending', 0])
            ->andWhere([
                'OR',
                ['date_sended' => 0],
                ['date_sended' => NULL]
            ])
            ->all();
        $aDirection1Trips = ArrayHelper::index($direction1_start_trips, 'id');

        // на направлении КА сегодня сколько рейсов в статусе "отправка начата"
        $direction2_start_trips = Trip::find()
            ->where(['direction_id' => 2])
            ->andWhere(['>', 'date_start_sending', 0])
            ->andWhere([
                'OR',
                ['date_sended' => 0],
                ['date_sended' => NULL]
            ])
            ->all();
        $aDirection2Trips = ArrayHelper::index($direction2_start_trips, 'id');


        // Данные для модального окна "Активные рейсы"

        // определяем для каждого направление наличие плохих рейсов
        // рейс считается плохим если:
        //  - 1. или если на рейсе кол-во мест суммарное больше кол-ва мест суммарного в машинах
        //  - 2. или если на рейсе с начатой отправкой есть заказ с is_confirmed != 1 (ПДТ не нажато)
        //  - 3. или если на рейсе с начатой отправкой есть заказ с неподтвержденной посадкой, при текущее время больше ВРПТ + 3 минуты
        $aTripsOrders = [];
        $aTripsTransports = [];
        $aTripsTripTransports = [];
        if(count($direction1_start_trips) > 0) {

            $orders = Order::find()
                ->where(['trip_id' => ArrayHelper::map($direction1_start_trips, 'id', 'id')])
                ->andWhere(['>', 'status_id', 0])
                ->andWhere(['!=', 'status_id', $canceled_order_status->id])
                ->all();
            foreach($orders as $order) {
                $aTripsOrders[$order->trip_id][] = $order;
                $aTripsTripTransports[$order->fact_trip_transport_id][] = $order;
            }
            $trips_transports = TripTransport::find()->where(['trip_id' => ArrayHelper::map($direction1_start_trips, 'id', 'id')])->all();
            $transports = Transport::find()->where(['id' => ArrayHelper::map($trips_transports, 'transport_id', 'transport_id')])->all();
            $aTransports = ArrayHelper::index($transports, 'id');
            foreach($trips_transports as $trip_transport) {
                $aTripsTransports[$trip_transport->trip_id][$trip_transport->id] = $aTransports[$trip_transport->transport_id];
            }

            foreach($aDirection1Trips as $trip_id => $trip)
            {

                $is_bad_trip = false;
                $bad_descriptions = [];

                $transports_places = 0;
                if(isset($aTripsTransports[$trip_id])) {
                    foreach ($aTripsTransports[$trip_id] as $transport) {
                        $transports_places += $transport->places_count;
                    }
                }

                $orders_places = 0;
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        $orders_places += $order->places_count;
                    }
                }

                // 1.1. мест в машинах меньше чем заказов на рейсе - не хорошо...
                if($transports_places < $orders_places) {
                    $is_bad_trip = true;
                    $bad_descriptions[] = 'проверить количество пассажиров';
                }

                // 1.2. мест в какой-либо машине меньше чем мест в привязанных к ней заказах
                //if(isset($aTripsOrders[$trip_id])) {
                    //echo "trips_transports:<pre>"; print_r($trips_transports); echo "</pre>";

                foreach($trips_transports as $trip_transport) {

                    if($trip_transport->trip_id == $trip_id) {
                        $transport_orders_count = 0;
                        if (isset($aTripsTripTransports[$trip_transport->id])) {
                            foreach ($aTripsTripTransports[$trip_transport->id] as $order) {
                                $transport_orders_count += $order->places_count;
                            }
                        }

                        $transport = $aTripsTransports[$trip_transport->trip_id][$trip_transport->id];
                        if ($transport->places_count < $transport_orders_count) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить количество пассажиров';
                            break;
                        }
                    }
                }
                //}

                // 2. если на рейсе есть заказ с is_confirmed != 1 (ПДТ не нажато) - тоже плохо
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        if ($order->is_confirmed != 1) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить ПДТ';
                            break;
                        }
                    }
                }

                // 3. если на рейсе есть заказ с неподтвержденной посадкой и текущее время больше ВРПТ + 3 минуты - плохо
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        if (empty($order->confirmed_time_sat) && time() > ($order->time_confirm + 180)) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить ВРПТ';
                            break;
                        }
                    }
                }


                // 4 если у водителя нет точек
                //if(isset($aTripsOrders[$trip_id])) {
                //echo "trips_transports:<pre>"; print_r($trips_transports); echo "</pre>";
                foreach($trips_transports as $trip_transport) {

                    if($trip_transport->trip_id == $trip_id) {
                        if (isset($aTripsTripTransports[$trip_transport->id]) && count($aTripsTripTransports[$trip_transport->id]) > 0) {

                            $is_sat_orders_count = 0;
                            $transport_without_orders = true;
                            foreach ($aTripsTripTransports[$trip_transport->id] as $order) {

                                if (!empty($order->time_sat)) {
                                    $transport_without_orders = false;
                                }

                                // проверка все ли точки посажены или есть непосаженные
                                if ($trip->use_mobile_app == 0) { // стандартный режим рейса
                                    if (!empty($order->time_sat)) {
                                        $is_sat_orders_count++;
                                    }
                                } else { // интерактивный режим рейса
                                    if (!empty($order->time_sat) && !empty($order->confirmed_time_sat)) {
                                        $is_sat_orders_count++;
                                    }
                                }
                            }

                            if ($is_sat_orders_count > 0 && $is_sat_orders_count == count($aTripsTripTransports[$trip_transport->id])) {
                                $is_bad_trip = true;
                                $bad_descriptions[] = 'у водителя все посажены'; // нет посаженных в машины
                                break;
                            }

                            if ($transport_without_orders) {
                                $is_bad_trip = true;
                                $bad_descriptions[] = 'у водителя нет точек';
                                break;
                            }


                        } else { // у рейсомашины $trip_transport нет заказов
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'у водителя нет заказов';
                            break;
                        }
                    }
                }
               // }


                $aActiveTrips[1][$trip_id] = [
                    'trip' => $trip,
                    'bad' => $is_bad_trip,
                    'bad_descriptions' => $bad_descriptions
                ];
            }
        }


        $aTripsOrders = [];
        $aTripsTransports = [];
        $aTripsTripTransports = [];
        if(count($direction2_start_trips) > 0) {

            $orders = Order::find()
                ->where(['trip_id' => ArrayHelper::map($direction2_start_trips, 'id', 'id')])
                ->andWhere(['>', 'status_id', 0])
                ->andWhere(['!=', 'status_id', $canceled_order_status->id])
                ->all();
            foreach($orders as $order) {
                $aTripsOrders[$order->trip_id][] = $order;
                $aTripsTripTransports[$order->fact_trip_transport_id][] = $order;
            }
            $trips_transports = TripTransport::find()->where(['trip_id' => ArrayHelper::map($direction2_start_trips, 'id', 'id')])->all();
            $transports = Transport::find()->where(['id' => ArrayHelper::map($trips_transports, 'transport_id', 'transport_id')])->all();
            $aTransports = ArrayHelper::index($transports, 'id');
            foreach($trips_transports as $trip_transport) {
                $aTripsTransports[$trip_transport->trip_id][$trip_transport->id] = $aTransports[$trip_transport->transport_id];
            }

            foreach($aDirection2Trips as $trip_id => $trip) {

                $is_bad_trip = false;
                $bad_descriptions = [];

                $transports_places = 0;
                if(isset($aTripsTransports[$trip_id])) {
                    foreach ($aTripsTransports[$trip_id] as $transport) {
                        $transports_places += $transport->places_count;
                    }
                }

                $orders_places = 0;
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        $orders_places += $order->places_count;
                    }
                }

                // 1.1. мест в машинах меньше чем заказов на рейсе - не хорошо...
                if($transports_places < $orders_places) {
                    $is_bad_trip = true;
                    $bad_descriptions[] = 'проверить количество пассажиров';
                }

                // 1.2. мест в какой-либо машине меньше чем мест в привязанных к ней заказах
                //if(isset($aTripsOrders[$trip_id])) {
                foreach($trips_transports as $trip_transport) {

                    if($trip_transport->trip_id == $trip_id) {
                        $transport_orders_count = 0;
                        if (isset($aTripsTripTransports[$trip_transport->id])) {
                            foreach ($aTripsTripTransports[$trip_transport->id] as $order) {
                                $transport_orders_count += $order->places_count;
                            }
                        }

                        $transport = $aTripsTransports[$trip_transport->trip_id][$trip_transport->id];
                        if ($transport->places_count < $transport_orders_count) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить количество пассажиров';
                            break;
                        }
                    }
                }
                //}

                // 2. если на рейсе есть заказ с is_confirmed != 1 (ПДТ не нажато) - тоже плохо
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        if ($order->is_confirmed != 1) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить ПДТ';
                            break;
                        }
                    }
                }

                // 3. если на рейсе есть заказ с неподтвержденной посадкой и текущее время больше ВРПТ + 3 минуты - плохо
                if(isset($aTripsOrders[$trip_id])) {
                    foreach ($aTripsOrders[$trip_id] as $order) {
                        if (empty($order->confirmed_time_sat) && time() > ($order->time_confirm + 180)) {
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'проверить ВРПТ';
                            break;
                        }
                    }
                }


                // 4 если у водителя нет точек
                //if(isset($aTripsOrders[$trip_id])) {
                foreach($trips_transports as $trip_transport) {

                    if($trip_transport->trip_id == $trip_id) {
                        if (isset($aTripsTripTransports[$trip_transport->id]) && count($aTripsTripTransports[$trip_transport->id]) > 0) {

                            $is_sat_orders_count = 0;
                            $transport_without_orders = true;
                            foreach ($aTripsTripTransports[$trip_transport->id] as $order) {

                                if (!empty($order->time_sat)) {
                                    $transport_without_orders = false;
                                }

                                // проверка все ли точки посажены или есть непосаженные
                                if ($trip->use_mobile_app == 0) { // стандартный режим рейса
                                    if (!empty($order->time_sat)) {
                                        $is_sat_orders_count++;
                                    }
                                } else { // интерактивный режим рейса
                                    if (!empty($order->time_sat) && !empty($order->confirmed_time_sat)) {
                                        $is_sat_orders_count++;
                                    }
                                }
                            }

                            if ($is_sat_orders_count > 0 && $is_sat_orders_count == count($aTripsTripTransports[$trip_transport->id])) {
                                $is_bad_trip = true;
                                $bad_descriptions[] = 'у водителя все посажены'; // нет посаженных в машины
                                break;
                            }

                            if ($transport_without_orders) {
                                $is_bad_trip = true;
                                $bad_descriptions[] = 'у водителя нет точек';
                                break;
                            }

                        } else { // у рейсомашины $trip_transport нет заказов
                            $is_bad_trip = true;
                            $bad_descriptions[] = 'у водителя нет заказов';
                            break;
                        }
                    }
                }
                //}

                $aActiveTrips[2][$trip_id] = [
                    'trip' => $trip,
                    'bad' => $is_bad_trip,
                    'bad_descriptions' => $bad_descriptions
                ];
            }
        }


        $direction1_has_bad_trip = false;
        $direction2_has_bad_trip = false;
        if(isset($aActiveTrips[1])) {
            foreach ($aActiveTrips[1] as $trip_id => $aTrips) {
                if ($aTrips['bad'] == true) {
                    $direction1_has_bad_trip = true;
                    break;
                }
            }
        }
        if(isset($aActiveTrips[2])) {
            foreach ($aActiveTrips[2] as $trip_id => $aTrips) {
                if ($aTrips['bad'] == true) {
                    $direction2_has_bad_trip = true;
                    break;
                }
            }
        }


        return [$aActiveTrips, $direction1_has_bad_trip, $direction2_has_bad_trip];
    }


    public static function updateActiveTripsModal() {

        list($aActiveTrips, $direction1_has_bad_trip, $direction2_has_bad_trip) = IncomingOrdersWidget::getActiveTripsData();
        $data = [
            'active_trips_modal_html' => Yii::$app->controller->renderPartial('@app/widgets/views/incoming-orders/active-trips-modal', ['aActiveTrips' => $aActiveTrips]),
            'direction1_has_bad_trip' => $direction1_has_bad_trip,
            'direction2_has_bad_trip' => $direction2_has_bad_trip,
            'direction1_trips_count' => isset($aActiveTrips[1]) ? count($aActiveTrips[1]) : 0,
            'direction2_trips_count' => isset($aActiveTrips[2]) ? count($aActiveTrips[2]) : 0,
        ];
        //SocketDemon::sendOutBrowserMessage('all_site_pages', [''], 'updateIncomingOrders', $data);
        SocketDemon::sendOutBrowserMessage('all_site_pages', [''], 'updateActiveTrips', $data);
    }

    public static function updateIncomingClientextOrders() {

        // ! временно не используется
        // данные для модального окна "Свежие заявки"
        $mobile_orders = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'external_id', 0])
            //->andWhere(['external_type' => 'client_server_client_ext'])
            ->andWhere(['external_type' => 'application'])
            ->all();

        $data = [
            'incoming_clientext_orders_modal_html' => Yii::$app->controller->renderPartial('@app/widgets/views/incoming-orders/incoming-mobile-orders-modal', ['mobile_orders' => $mobile_orders]),
            'incoming_clientext_orders_count' => count($mobile_orders),
        ];
        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingClientextOrders', $data);
    }

    public static function updateIncomingRequestOrders() {

        // данные для модального окна "Свежие заявки"
        $client_orders = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'external_id', 0])
            ->andWhere(['external_type' => 'client_server_request'])
            ->all();


        $data = [
            'incoming_request_orders_modal_html' => Yii::$app->controller->renderPartial('@app/widgets/views/incoming-orders/incoming-client-orders-modal', [
                'client_orders' => $client_orders,
            ]),
            'incoming_request_orders_count' => count($client_orders),
        ];
        SocketDemon::sendOutBrowserMessageInstant('all_site_pages', [''], 'updateIncomingRequestOrders', $data);
    }
}