<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

class Loyality extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loyality';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'past_sent_orders', 'past_sent_orders_places', 'past_canceled_orders',
                'past_canceled_orders_places', 'past_fixed_price_orders_places', 'past_is_not_places',
                'past_informer_beznal_orders_places', 'past_prize_trip_count', 'past_penalty', 'present_sent_orders',
                'present_sent_orders_places', 'present_canceled_orders', 'present_canceled_orders_places',
                'present_fixed_price_orders_places', 'present_is_not_places', 'present_informer_beznal_orders_places',
                'present_prize_trip_count', 'present_penalty', 'total_sent_orders',
                'total_sent_orders_places', 'total_canceled_orders', 'total_canceled_orders_places',
                'total_fixed_price_orders_places', 'total_is_not_places', 'total_informer_beznal_orders_places',
                'total_prize_trip_count', 'total_penalty',

                'past_i6', 'past_i7', 'past_i8',
                'present_i6', 'present_i7', 'present_i8',
                'total_i6', 'total_i7', 'total_i8'

            ], 'integer'],
            [[
                'past_i1', 'past_i2', 'past_i3', 'past_i4', 'past_i5',
                'present_i1', 'present_i2', 'present_i3', 'present_i4', 'present_i5',
                'total_i1', 'total_i2', 'total_i3', 'total_i4', 'total_i5',
                'loyalty_indicator'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Клиент',
            'past_sent_orders' => 'прошлое: отправлено заказов',
            'past_sent_orders_places' => 'прошлое: отправлено мест в заказах',
            'past_canceled_orders' => 'прошлое: отменено заказов',
            'past_canceled_orders_places' => 'прошлое: отменено мест в заказах',
            'past_fixed_price_orders_places' => 'прошлое: мест в заказах по фикс.цене',
            'past_is_not_places' => 'прошлое: посылок',
            'past_informer_beznal_orders_places' => 'прошлое: поездок с б/н оплатой',
            'past_prize_trip_count' => 'прошлое: получено призовых поездок',
            'past_penalty' => 'прошлое: штрафов',
            'past_i1' => 'прошлое: усредненный интервал между временем создания заказа и временем подтвержденной посадки в т/с в сек',
            'past_i2' => 'прошлое: усредненный  интервал между временем создания заказа и временем отмены заказа в сек',
            'past_i3' => 'прошлое: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек',
            'past_i4' => 'прошлое: усредненный интервал между временем создания заказа и первым нажатием кнопки записать',
            'past_i5' => 'прошлое: усредненный интервал между временем создания заказа и временем отмены',
            'past_i6' => 'прошлое: частота поездок (среднее время между началами кругов)',
            'past_i7' => 'прошлое: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)',
            'past_i8' => 'прошлое: отношение реально завершенных к общему количеству кругов',

            'present_sent_orders' => 'настоящее: отправлено заказов',
            'present_sent_orders_places' => 'настоящее: отправлено мест в заказах',
            'present_canceled_orders' => 'настоящее: отменено заказов',
            'present_canceled_orders_places' => 'настоящее: отменено мест в заказах',
            'present_fixed_price_orders_places' => 'настоящее: мест в заказах по фикс.цене',
            'present_is_not_places' => 'настоящее: посылок',
            'present_informer_beznal_orders_places' => 'настоящее: поездок с б/н оплатой',
            'present_prize_trip_count' => 'настоящее: получено призовых поездок',
            'present_penalty' => 'настоящее: штрафов',
            'present_i1' => 'настоящее: усредненный интервал между временем создания заказа и временем подтвержденной посадки в т/с в сек',
            'present_i2' => 'настоящее: усредненный  интервал между временем создания заказа и временем отмены заказа в сек',
            'present_i3' => 'настоящее: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек',
            'present_i4' => 'настоящее: усредненный интервал между временем создания заказа и первым нажатием кнопки записать',
            'present_i5' => 'настоящее: усредненный интервал между временем создания заказа и временем отмены',
            'present_i6' => 'настоящее: частота поездок (среднее время между началами кругов)',
            'present_i7' => 'настоящее: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)',
            'present_i8' => 'настоящее: отношение реально завершенных к общему количеству кругов',

            'total_sent_orders' => 'суммарное: отправлено заказов',
            'total_sent_orders_places' => 'суммарное: отправлено мест в заказах',
            'total_canceled_orders' => 'суммарное: отменено заказов',
            'total_canceled_orders_places' => 'суммарное: отменено мест в заказах',
            'total_fixed_price_orders_places' => 'суммарное: мест в заказах по фикс.цене',
            'total_is_not_places' => 'суммарное: посылок',
            'total_informer_beznal_orders_places' => 'суммарное: поездок с б/н оплатой',
            'total_prize_trip_count' => 'суммарное: получено призовых поездок',
            'total_penalty' => 'суммарное: штрафов',
            'total_i1' => 'суммарное: усредненный интервал между временем создания заказа и временем подтвержденной посадки в т/с в сек',
            'total_i2' => 'суммарное: усредненный  интервал между временем создания заказа и временем отмены заказа в сек',
            'total_i3' => 'суммарное: усредненный интервал между временем отмены заказа и временем первой точки рейса в сек',
            'total_i4' => 'суммарное: усредненный интервал между временем создания заказа и первым нажатием кнопки записать',
            'total_i5' => 'суммарное: усредненный интервал между временем создания заказа и временем отмены',
            'total_i6' => 'суммарное: частота поездок (среднее время между началами кругов)',
            'total_i7' => 'суммарное: время возврата (среднее время между началом круга и предполагаемым завершением либо реальным завершением круга)',
            'total_i8' => 'суммарное: отношение реально завершенных к общему количеству кругов',

            'loyalty_indicator' => 'индикатор лояльности',
        ];
    }


    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    public static function rewriteClientsCounters($limit, $client_id_from) {

        // <!-- Дублирую код 1
//        $clients = Client::find()
//            ->where(['id' => 1])
//            //->offset($step*$clients_count)
//            //->limit($clients_count)
//            ->all();

        $clients = Client::find()
            ->where(['>', 'id', $client_id_from])
            ->orderBy(['id' => SORT_ASC])
            ->limit($limit)
            ->all();

        $order_query = Order::find()->where(['client_id' => ArrayHelper::map($clients, 'id', 'id')]);
        $orders = $order_query
            ->andWhere(['>', 'status_id', 0])
            ->all();

        $aOrders = [];
        foreach($orders as $order) {
            $aOrders[$order->client_id][] = $order;
        }

        $trips = Trip::find()->where(['id' => ArrayHelper::map($orders, 'trip_id', 'trip_id')])->all();
        $aTrips =  ArrayHelper::index($trips, 'id');

        $informer_offices = InformerOffice::find()->all();
        $aInformerOffices = ArrayHelper::map($informer_offices, 'id', 'cashless_payment');

        $canceled_order_status_id = OrderStatus::getByCode('canceled')->id;
        $sent_order_status_id = OrderStatus::getByCode('sent')->id;

        $delimiter_data = "01.01.".date("Y");
        //$delimiter_data = "01.01.2020";
        $unixtime_delimiter_data = strtotime($delimiter_data);
        // Дублирую код 1 -->

        // дата заказа попадает в диапозон текущего года от 1 января до 13 января (до 14 января 0:00:00)
        $unixdate_1jan = strtotime('01.01.'.date('Y'));
        $unixdate_14jan = strtotime('14.01.'.date('Y'));

        $aClientsData = [];

        $aFields = [
            //'id' => 0,
            'current_year_sended_places' => 0,//'Число отправленных мест',
            'current_year_sended_orders' => 0,//'Число отправленных заказов',

            'current_year_sended_standart_places' => 0, // Число отправленных мест на стандартных рейсах
            'current_year_sended_standart_orders' => 0, // Число отправленных заказов на стандартных рейсах
            'current_year_sended_commercial_places' => 0, // Число отправленных мест на коммерческих рейсах
            'current_year_sended_commercial_orders' => 0, // Число отправленных заказов на коммерческих рейсах
            'current_year_sended_113_places' => 0, // Число отправленных мест всего с 1 по 13 января включительно
            'current_year_sended_113_orders' => 0, // Число отправленных заказов всего с 1 по 13 января включительно

            'current_year_canceled_places' => 0,//'Число отмененных мест',
            'current_year_canceled_orders' => 0,//'Число отмененных заказов',

            //'current_year_places_reliability' => 0,//'Надежность по местам в текущем году' -  отношение отправленных мест к общему количеству заказанных мест
            //'current_year_orders_reliability' => 0,//'Надежность по заказам в текущем году',
            'current_year_canceled_orders_1h' => 0, // количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса
            'current_year_canceled_orders_12h' => 0, // количество отмененных заказов менее чем за 12 часов до последней точки рейса


            'current_year_sended_prize_places' => 0,//'Число отправленных призовых поездок в текущем году',
            'current_year_penalty' => 0,//'Число штрафов в текущем году',
            'current_year_sended_fixprice_places' => 0,//'Число мест по фикс.цене отправленных в текущем году',
            'current_year_sended_fixprice_orders' => 0,//'Число заказов по фикс.цене в текущем году',
            'current_year_sended_informer_beznal_places' => 0,//'Число мест с безналичной оплатой в текущем году',
            'current_year_sended_informer_beznal_orders' => 0,//'Число заказов с безналичной оплатой в текущем году',
            'current_year_sended_isnotplaces_orders' => 0,//'Число посылок в текущем году',

            'past_years_sended_places' => 0,//'Число отправленных мест всего по прошлым периодам',
            'past_years_sended_orders' => 0,//'Число отмененных мест всего по прошлым периодам',
            'past_years_canceled_places' => 0,//'Число отправленных заказов по прошлым периодам',
            'past_years_canceled_orders' => 0,//'Число отмененных заказов по прошлым периодам',

            'past_years_canceled_orders_1h' => 0, // количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса
            'past_years_canceled_orders_12h' => 0, // количество отмененных заказов менее чем за 12 часов до последней точки рейса

            'past_years_sended_prize_places' => 0,//'Количество отправленных призовых поездок по прошлым периодам',
            'past_years_penalty' => 0,//'Количество штрафов по прошлым периодам',
            'past_years_sended_fixprice_places' => 0, //'Количество мест по фикс.цене по прошлым периодам',
            'past_years_sended_fixprice_orders' => 0,//'Количество заказов по фикс.цене по прошлым периодам',

            'past_years_sended_informer_beznal_places' => 0,//'Количество мест с безналичной оплатой по прошлым периодам',
            'past_years_sended_informer_beznal_orders' => 0,//'Количество заказов с безналичной оплатой по прошлым периодам',
            'past_years_sended_isnotplaces_orders' => 0,//'Количество посылок по прошлым периодам',
        ];


        foreach($clients as $client)
        {
            $aL = $aFields;
            $aL['id'] = $client->id;

            // <!-- копируем блок 2
            //$total_client_orders_i = 0;
            // -->

            // <!-- копируем блок 3
            $aClientOrders = isset($aOrders[$client->id]) ? $aOrders[$client->id] : [];

            // отсортируем заказа по времени создания
            if(count($aClientOrders) > 0) {

                $aSortClientOrder = [];
                foreach($aClientOrders as $order) {

                    if(isset($aTrips[$order->trip_id])) {
                        // $aSortClientOrder[$order->trip_id] = $order; // ошибка, схлопывание массива
                        $aSortClientOrder[$order->trip_id.'_'.$order->id] = $order;
                    }
                }
                //echo "count_aSortClientOrder=".count($aSortClientOrder)."<br />";
                ksort($aSortClientOrder);

                $aClientOrders = $aSortClientOrder;
            }
            // -->


            $current_year_total_orders = 0;
            $current_year_total_places = 0;
            foreach($aClientOrders as $order)
            {
                if(!isset($aTrips[$order->trip_id])) {
                    throw new ErrorException('Рейс для заказа order_id='.$order->id);
                }
                $trip = isset($aTrips[$order->trip_id]) ? $aTrips[$order->trip_id] : null;

                if($order->date < $unixtime_delimiter_data) { // заказы прошлых лет

                    if($order->status_id == $sent_order_status_id) {

                        $aL['past_years_sended_orders'] += 1;
                        $aL['past_years_sended_places'] += $order->places_count;

                        if($order->use_fix_price == 1) {
                            $aL['past_years_sended_fixprice_orders'] += 1;
                            $aL['past_years_sended_fixprice_places'] += $order->places_count;
                        }

                        if($order->is_not_places == 1) {
                            $aL['past_years_sended_isnotplaces_orders'] += 1;
                        }

//                        if($order->informerOffice != null && $order->informerOffice->cashless_payment == 1) {
//                            $aL['past_years_sended_informer_beznal_orders'] += 1;
//                            $aL['past_years_sended_informer_beznal_places'] += $order->places_count;
//                        }

                        if($order->informer_office_id > 0 && isset($aInformerOffices[$order->informer_office_id]) && $aInformerOffices[$order->informer_office_id] == 1) {
                            $aL['past_years_sended_informer_beznal_orders'] += 1;
                            $aL['past_years_sended_informer_beznal_places'] += $order->places_count;
                        }

                        $aL['past_years_sended_prize_places'] += $order->prize_trip_count;
                        if($order->has_penalty == 1) {
                            $aL['past_years_penalty'] += 1;
                        }
                    }

                    if($order->status_id == $canceled_order_status_id) {

                        $aL['past_years_canceled_orders'] += 1;
                        $aL['past_years_canceled_places'] += $order->places_count;


                        if($trip != null && $order->cancellation_click_time > 0) {

                            // количество отмененных заказов менее чем за 1 час до первой точки рейса
                            //  или на протяжении 3 часов после рейса
                            //list($trip_minutes, $trip_seconds) = explode(':', $trip->start_time);
                            //$trip_start_time = $trip->date + 60*$trip_minutes + $trip_seconds;
                            list($trip_hours, $trip_mins) = explode(':', $trip->start_time);
                            $trip_start_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                            if(
                                ($trip_start_time - $order->cancellation_click_time < 3600)
                                && ($trip_start_time - $order->cancellation_click_time > - 10800)
                            ) {
                                $aL['past_years_canceled_orders_1h'] += 1;
                            }

                            // количество отмененных заказов менее чем за 12 часов до последней точки рейса
                            //list($trip_minutes, $trip_seconds) = explode(':', $trip->end_time);
                            //$trip_end_time = strtotime($order->date) + 60*$trip_minutes + $trip_seconds;
                            list($trip_hours, $trip_mins) = explode(':', $trip->end_time);
                            $trip_end_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                            if(
                            ($trip_end_time - $order->cancellation_click_time < 43200) // 12 часов
                            ) {
                                $aL['past_years_canceled_orders_12h'] += 1;
                            }
                        }

                    }

                }else { // заказы из текущего года

                    //$aClientFields['current_year_sended_orders']++;
                    //$aClientFields['past_years_sended_places'] += $order->places_count;

                    if($order->status_id == $sent_order_status_id)
                    {
                        $aL['current_year_sended_orders'] += 1;
                        $aL['current_year_sended_places'] += $order->places_count;


                        if($order->date > $unixdate_1jan && $order->date < $unixdate_14jan) {

                            $aL['current_year_sended_113_orders'] += 1;
                            $aL['current_year_sended_113_places'] += $order->places_count;

                        }else {

                            if($trip->commercial == true) {

                                $aL['current_year_sended_commercial_orders'] += 1;
                                $aL['current_year_sended_commercial_places'] += $order->places_count;

                            }else {

                                $aL['current_year_sended_standart_orders'] += 1;
                                $aL['current_year_sended_standart_places'] += $order->places_count;
                            }
                        }


                        if($order->use_fix_price == 1) {
                            $aL['current_year_sended_fixprice_orders'] += 1;
                            $aL['current_year_sended_fixprice_places'] += $order->places_count;
                        }

                        if($order->is_not_places == 1) {
                            $aL['current_year_sended_isnotplaces_orders'] += 1;
                        }

//                        if($order->informerOffice != null && $order->informerOffice->cashless_payment == 1) {
//                            $aL['current_year_sended_informer_beznal_orders'] += 1;
//                            $aL['current_year_sended_informer_beznal_places'] += $order->places_count;
//                        }

                        if($order->informer_office_id > 0 && isset($aInformerOffices[$order->informer_office_id]) && $aInformerOffices[$order->informer_office_id] == 1) {
                            $aL['current_year_sended_informer_beznal_orders'] += 1;
                            $aL['current_year_sended_informer_beznal_places'] += $order->places_count;
                        }

                        $aL['current_year_sended_prize_places'] += $order->prize_trip_count;
                        if($order->has_penalty == 1) {
                            $aL['current_year_penalty'] += 1;
                        }

                        $current_year_total_orders += 1;
                        $current_year_total_places += $order->places_count;
                    }

                    if($order->status_id == $canceled_order_status_id) {

                        $aL['current_year_canceled_orders'] += 1;
                        $aL['current_year_canceled_places'] += $order->places_count;

                        $current_year_total_orders += 1;
                        $current_year_total_places += $order->places_count;


                        if($trip != null && $order->cancellation_click_time > 0) {

                            // количество отмененных заказов менее чем за 1 час до первой точки рейса
                            //  или на протяжении 3 часов после рейса
                            //list($trip_minutes, $trip_seconds) = explode(':', $trip->start_time);
                            //$trip_start_time = $trip->date + 60*$trip_minutes + $trip_seconds;
                            list($trip_hours, $trip_mins) = explode(':', $trip->start_time);
                            $trip_start_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                            if(
                                ($trip_start_time - $order->cancellation_click_time < 3600)
                                && ($trip_start_time - $order->cancellation_click_time > - 10800)
                            ) {
                                $aL['current_year_canceled_orders_1h'] += 1;
                            }

                            // количество отмененных заказов менее чем за 12 часов до последней точки рейса
                            //list($trip_minutes, $trip_seconds) = explode(':', $trip->end_time);
                            //$trip_end_time = $trip->date + 60*$trip_minutes + $trip_seconds;

                            list($trip_hours, $trip_mins) = explode(':', $trip->end_time);
                            $trip_end_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                            if(
                                ($trip_end_time - $order->cancellation_click_time < 43200) // 12 часов
                            ) {
                                $aL['current_year_canceled_orders_12h'] += 1;
                            }
                        }
                    }
                }
            }


            if($current_year_total_orders > 0) {
                //$aL['current_year_orders_reliability'] = round(100*$aL['current_year_sended_orders'] / $current_year_total_orders, 2);
            }

            if($current_year_total_places > 0) {
                //$aL['current_year_places_reliability'] = round(100*$aL['current_year_sended_places'] / $current_year_total_places, 2);
            }


            $aClientsData[$client->id] = $aL;
        }



        $aFieldsSqls = [];
        foreach($aFields as $field => $val) {

            $aFieldSqls = [];
            foreach($aClientsData as $client_id => $aClientData) {
                $aFieldSqls[] = 'WHEN id='.$client_id.' THEN '.$aClientData[$field].' ';
            }

            $aFieldsSqls[] = $field.'= CASE '.implode('', $aFieldSqls).' END';
        }

        $sql = 'UPDATE `client` SET '.implode(', ', $aFieldsSqls).' WHERE id IN ('.implode(',', array_keys($aClientsData)).')';
        Yii::$app->db->createCommand($sql)->execute();

        //$aClients = ArrayHelper::index($clients, 'id');
        return $clients[count($clients) - 1]->id;
    }




    public static function rewriteLoyality($step = 0, $clients_count=1000, $unixtime_date_from = 0, $unixtime_date_to = 0) {

        // <!-- Дублирую код 1
        $clients = Client::find()
            //->where(['id' => 24767])
            ->offset($step*$clients_count)
            ->limit($clients_count)
            ->all();
        $order_query = Order::find()->where(['client_id' => ArrayHelper::map($clients, 'id', 'id')]);
        if($unixtime_date_from > 0) {
            $order_query = $order_query->andWhere(['>=', 'date', $unixtime_date_from]);
        }
        if($unixtime_date_to > 0) {
            $order_query = $order_query->andWhere(['<=', 'date', $unixtime_date_to]);
        }
        $orders = $order_query
            ->andWhere(['>', 'status_id', 0])
            ->all();

        $aOrders = [];
        foreach($orders as $order) {
            $aOrders[$order->client_id][] = $order;
        }

        $trips = Trip::find()->where(['id' => ArrayHelper::map($orders, 'trip_id', 'trip_id')])->all();
        $aTrips =  ArrayHelper::index($trips, 'id');

        $canceled_order_status_id = OrderStatus::getByCode('canceled')->id;
        $sent_order_status_id = OrderStatus::getByCode('sent')->id;

        $delimiter_data = "01.01.".date("Y");
        $unixtime_delimiter_data = strtotime($delimiter_data);
        // Дублирую код 1 -->

        $aLoyalities = [];
        $aFields = [
            'client_id' => 0,
            'past_sent_orders' => 0,
            'past_sent_orders_places' => 0,
            'past_canceled_orders' => 0,
            'past_canceled_orders_places' => 0,
            'past_fixed_price_orders_places' => 0,
            'past_is_not_places' => 0,
            'past_informer_beznal_orders_places' => 0,
            'past_prize_trip_count' => 0,
            'past_penalty' => 0,
            'past_i1' => 0,
            'past_i2' => 0,
            'past_i3' => 0,
            'past_i4' => 0,
            'past_i5' => 0,
            'past_i6' => 0,
            'past_i7' => 0,
            'past_i8' => 0,

            'present_sent_orders' => 0,
            'present_sent_orders_places' => 0,
            'present_canceled_orders' => 0,
            'present_canceled_orders_places' => 0,
            'present_fixed_price_orders_places' => 0,
            'present_is_not_places' => 0,
            'present_informer_beznal_orders_places' => 0,
            'present_prize_trip_count' => 0,
            'present_penalty' => 0,
            'present_i1' => 0,
            'present_i2' => 0,
            'present_i3' => 0,
            'present_i4' => 0,
            'present_i5' => 0,
            'present_i6' => 0,
            'present_i7' => 0,
            'present_i8' => 0,

            'total_sent_orders' => 0,
            'total_sent_orders_places' => 0,
            'total_canceled_orders' => 0,
            'total_canceled_orders_places' => 0,
            'total_fixed_price_orders_places' => 0,
            'total_is_not_places' => 0,
            'total_informer_beznal_orders_places' => 0,
            'total_prize_trip_count' => 0,
            'total_penalty' => 0,
            'total_i1' => 0,
            'total_i2' => 0,
            'total_i3' => 0,
            'total_i4' => 0,
            'total_i5' => 0,
            'total_i6' => 0,
            'total_i7' => 0,
            'total_i8' => 0,
        ];

        foreach($clients as $client) {

//            client_id
//
//            past_sent_orders          // прошлое: отправлено заказов
//            past_sent_orders_places        // прошлое: отправлено мест в заказах
//            past_canceled_orders           // прошлое: отменено заказов
//            past_canceled_orders_places    // прошлое: отменено мест в заказах
//            past_fixed_price_orders_places // прошлое: мест в заказах по фикс.цене
//            past_is_not_places             // прошлое: посылок
//            past_informer_beznal_orders_places // прошлое: поездок с б/н оплатой
//            past_prize_trip_count     // прошлое: получено призовых поездок
//            past_penalty              // прошлое: штрафов
//            past_i1 // усредненный интервал между временем создания заказа и временем посадки в т/с в сек
//            past_i2 // усредненный интервал между временем создания заказа и временем отмены заказа в сек
//            past_i3 // усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
//            past_i4 // усредненный интервал между временем создания заказа и первым нажатием кнопки записать
//            past_i5 // усредненный интервал между временем создания заказа и временем отмены
//            past_i6
//            past_i7
//            past_i8
//
//            present_sent_orders
//            present_sent_orders_places
//            present_canceled_orders
//            present_canceled_orders_places
//            present_fixed_price_orders_places
//            present_is_not_places
//            present_informer_beznal_orders_places
//            present_prize_trip_count
//            present_penalty
//            present_i1 // усредненный интервал между временем создания заказа и временем посадки в т/с в сек
//            present_i2 // усредненный интервал между временем создания заказа и временем отмены заказа в сек
//            present_i3 // усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
//            present_i4 // усредненный интервал между временем создания заказа и первым нажатием кнопки записать
//            present_i5 // усредненный интервал между временем создания заказа и временем отмены
//            present_i6
//            present_i7
//            present_i8
//
//            total_sent_orders
//            total_sent_orders_places
//            total_canceled_orders
//            total_canceled_orders_places
//            total_fixed_price_orders_places
//            total_is_not_places
//            total_informer_beznal_orders_places
//            total_prize_trip_count
//            total_penalty
//            total_i1 // усредненный интервал между временем создания заказа и временем посадки в т/с в сек
//            total_i2 // усредненный интервал между временем создания заказа и временем отмены заказа в сек
//            total_i3 // усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
//            total_i4 // усредненный интервал между временем создания заказа и первым нажатием кнопки записать
//            total_i5 // усредненный интервал между временем создания заказа и временем отмены
//            total_i6
//            total_i7
//            total_i8
//
//            loyalty_indicator // индикатор лояльности

            $past_i1 = [];
            $past_i2 = [];
            $past_i3 = [];
            $past_i4 = [];
            $past_i5 = [];

            $present_i1 = [];
            $present_i2 = [];
            $present_i3 = [];
            $present_i4 = [];
            $present_i5 = [];

            $total_i1 = [];
            $total_i2 = [];
            $total_i3 = [];
            $total_i4 = [];
            $total_i5 = [];


            $aL = $aFields;
            $aL['client_id'] = $client->id;

            // <!-- копируем блок 2
            $total_client_orders_i = 0;
            // -->


            $total_direction = 0;

            $aPastOrderCircles = [];

            // <!-- копируем блок 3
            $aClientOrders = isset($aOrders[$client->id]) ? $aOrders[$client->id] : [];

            // отсортируем заказа по времени создания
            if(count($aClientOrders) > 0) {
                $aSortClientOrder = [];

                foreach($aClientOrders as $order) {
                    if(isset($aTrips[$order->trip_id])) {
                        $aSortClientOrder[$order->trip_id] = $order;
                    }
                }
                //echo "count_aSortClientOrder=".count($aSortClientOrder)."<br />";
                ksort($aSortClientOrder);

                $aClientOrders = $aSortClientOrder;
            }
            // -->


            foreach($aClientOrders as $order)
            {
                $total_client_orders_i++;
                if($total_client_orders_i == 1) {
                    $total_direction = $order->direction_id;
                }

                // запись цикла (начала и закрывающей части)
                if($order->direction_id == $total_direction) {

                    //list($trip_minutes, $trip_seconds) = explode(':', $aTrips[$order->trip_id]->end_time);
                    //$new_circle_time_start = strtotime($order->date) + 60*$trip_minutes + $trip_seconds;
                    $temp_trip = $aTrips[$order->trip_id];
                    list($trip_hours, $trip_mins) = explode(':', $temp_trip->end_time);
                    $new_circle_time_start = $temp_trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                    // смотрю закрыт ли предыдущий круг...
                    if(count($aPastOrderCircles) > 0) {
                        $last_circle = $aPastOrderCircles[count($aPastOrderCircles) - 1];
                        if(!isset($last_circle['end_order_time'])) { // если предыдущий круг не закрыт, то создадим закрытие круга "виртуально"
                            // время закрытия предудыщего круга равно время открытия нового(текущего круга) минус время открытия предыдущего круга
                            $last_circle['end_order_time'] = ($new_circle_time_start - $last_circle['start_order_time'])/2;
                            $last_circle['end_order_is_real'] = false;

                            $aPastOrderCircles[count($aPastOrderCircles) - 1] = $last_circle; // обратно сохраняем в общим массив предудущий круг
                        }
                    }

                    // создание нового круга
                    $aPastOrderCircles[] = [
                        'start_order_time' => $new_circle_time_start
                    ];

                }else {  // закрывающий круг

                    $last_circle = $aPastOrderCircles[count($aPastOrderCircles) - 1];

                    //list($trip_minutes, $trip_seconds) = explode(':', $aTrips[$order->trip_id]->end_time);
                    //$circle_end = strtotime($order->date) + 60*$trip_minutes + $trip_seconds;

                    $temp_trip = $aTrips[$order->trip_id];
                    list($trip_hours, $trip_mins) = explode(':', $temp_trip->end_time);
                    $circle_end = $temp_trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

                    $last_circle['end_order_time'] = $circle_end;
                    $last_circle['end_order_is_real'] = true;
                }



                if($order->date < $unixtime_delimiter_data) {

//                    $past_client_orders_i++;
//                    if($past_client_orders_i == 1) {
//                        $past_direction = $order->direction_id;
//                    }

                    if($order->status_id == $sent_order_status_id) {
                        $aL['past_sent_orders'] += 1;
                        $aL['past_sent_orders_places'] += $order->places_count;

                        if($order->use_fix_price == 1) {
                            $aL['past_fixed_price_orders_places'] += $order->places_count;
                        }

                        if($order->is_not_places == 1) {
                            $aL['past_is_not_places'] += 1;
                        }

                        if($order->informerOffice != null && $order->informerOffice->cashless_payment == 1) {
                            $aL['past_informer_beznal_orders_places'] += $order->places_count;
                        }

                        //$aL['past_prize_trip_count += $order->prizeTripCount;
                        $aL['past_prize_trip_count'] += $order->prize_trip_count;
                        if($order->has_penalty == 1) {
                            $aL['past_penalty'] += 1;
                        }
                    }

                    if($order->status_id == $canceled_order_status_id) {

                        $aL['past_canceled_orders'] += 1;
                        $aL['past_canceled_orders_places'] += $order->places_count;

                        $aL['total_canceled_orders'] += 1;
                        $aL['total_canceled_orders_places'] += $order->places_count;

                        // i2 - усредненный интервал между временем создания заказа и временем отмены заказа в сек
                        if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                            $past_i2[] = $order->cancellation_click_time - $order->created_at;
                        }

                        // i3 - усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
                        $trip = $order->trip;
                        $aTripStartTime = explode(':', $trip->start_time);
                        if($order->cancellation_click_time > 0) {
                            $past_i3[] = $trip->date + 3600 * $aTripStartTime[0] + 60 * $aTripStartTime[1] - $order->cancellation_click_time;
                        }

                        // i5 - усредненный интервал между временем создания заказа и временем отмены
                        if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                            $past_i5[] = $order->cancellation_click_time - $order->created_at;
                        }
                    }

                    if($order->confirmed_time_sat > 0 && $order->created_at > 0) {
                        $past_i1[] = $order->confirmed_time_sat - $order->created_at;
                    }

                    // i4 - усредненный интервал между временем создания заказа и первым нажатием кнопки записать
                    if($order->first_writedown_click_time > 0 && $order->created_at > 0) {
                        $past_i4[] = $order->first_writedown_click_time - $order->created_at;
                    }


                }else
                {

//                    $present_orders_i++;
//                    if($present_orders_i == 1) {
//                        $present_direction = $order->direction_id;
//                    }

                    if($order->status_id == $sent_order_status_id) {
                        $aL['present_sent_orders'] += 1;
                        $aL['present_sent_orders_places'] += $order->places_count;

                        if($order->use_fix_price == 1) {
                            $aL['present_fixed_price_orders_places'] += $order->places_count;
                        }

                        if($order->is_not_places == 1) {
                            $aL['present_is_not_places'] += 1;
                        }

                        if($order->informerOffice != null && $order->informerOffice->cashless_payment == 1) {
                            $aL['present_informer_beznal_orders_places'] += $order->places_count;
                        }

                        //$aL['present_prize_trip_count'] += $order->prizeTripCount;
                        $aL['present_prize_trip_count'] += $order->prize_trip_count;
                        if($order->has_penalty == 1) {
                            $aL['present_penalty'] += 1;
                        }
                    }

                    if($order->status_id == $canceled_order_status_id) {

                        $aL['present_canceled_orders'] += 1;
                        $aL['present_canceled_orders_places'] += $order->places_count;

                        $aL['total_canceled_orders'] += 1;
                        $aL['total_canceled_orders_places'] += $order->places_count;

                        // i2 - усредненный интервал между временем создания заказа и временем отмены заказа в сек
                        if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                            $present_i2[] = $order->cancellation_click_time - $order->created_at;
                        }

                        // i3 - усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
                        $trip = $order->trip;
                        $aTripStartTime = explode(':', $trip->start_time);
                        if($order->cancellation_click_time > 0) {
                            $present_i3[] = $trip->date + 3600 * $aTripStartTime[0] + 60 * $aTripStartTime[1] - $order->cancellation_click_time;
                        }

                        // i5 - усредненный интервал между временем создания заказа и временем отмены
                        if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                            $present_i5[] = $order->cancellation_click_time - $order->created_at;
                        }
                    }


                    if($order->confirmed_time_sat > 0 && $order->created_at > 0) {
                        $present_i1[] = $order->confirmed_time_sat - $order->created_at;
                    }

                    // i4 - усредненный интервал между временем создания заказа и первым нажатием кнопки записать
                    if($order->first_writedown_click_time > 0 && $order->created_at > 0) {
                        $present_i4[] = $order->first_writedown_click_time - $order->created_at;
                    }

                }

                if($order->status_id == $sent_order_status_id) {

                    $aL['total_sent_orders'] += 1;
                    $aL['total_sent_orders_places'] += $order->places_count;

                    if($order->use_fix_price == 1) {
                        $aL['total_fixed_price_orders_places'] += $order->places_count;
                    }

                    if($order->is_not_places == 1) {
                        $aL['total_is_not_places'] += 1;
                    }

                    if($order->informerOffice != null && $order->informerOffice->cashless_payment == 1) {
                        $aL['total_informer_beznal_orders_places'] += $order->places_count;
                    }

                    // $aL['total_prize_trip_count'] += $order->prizeTripCount;
                    $aL['total_prize_trip_count'] += $order->prize_trip_count;
                    if($order->has_penalty == 1) {
                        $aL['total_penalty'] += 1;
                    }
                }


                if($order->status_id == $canceled_order_status_id) {
//                    $aL['total_canceled_orders'] += 1;
//                    $aL['total_canceled_orders_places'] += $order->places_count;

                    // i2 - усредненный интервал между временем создания заказа и временем отмены заказа в сек
                    if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                        $total_i2[] = $order->cancellation_click_time - $order->created_at;
                    }

                    // i3 - усредненный интервал между временем отмены заказа и временем первой точки рейса в сек
                    $trip = $order->trip;
                    $aTripStartTime = explode(':', $trip->start_time);
                    if($order->cancellation_click_time > 0) {
                        $total_i3[] = $trip->date + 3600 * $aTripStartTime[0] + 60 * $aTripStartTime[1] - $order->cancellation_click_time;
                    }

                    // i5 - усредненный интервал между временем создания заказа и временем отмены
                    if($order->cancellation_click_time > 0 && $order->created_at > 0) {
                        $total_i5[] = $order->cancellation_click_time - $order->created_at;
                    }
                }

                //i1 - усредненный интервал между временем создания заказа и временем посадки в т/с в сек
                if($order->confirmed_time_sat > 0 && $order->created_at > 0) {
                    $total_i1[] = $order->confirmed_time_sat - $order->created_at;
                }

                // i4 - усредненный интервал между временем создания заказа и первым нажатием кнопки записать
                if($order->first_writedown_click_time > 0 && $order->created_at > 0) {
                    $total_i4[] = intval($order->first_writedown_click_time) - intval($order->created_at);
                }
            }



            if(count($past_i1) > 0) {
                foreach ($past_i1 as $ii1) {
                    $aL['past_i'] += $ii1;
                }
                $aL['past_i1'] = $aL['past_i1'] / count($past_i1);
            }
            if(count($past_i2) > 0) {
                foreach ($past_i2 as $ii2) {
                    $aL['past_i2'] += $ii2;
                }
                $aL['past_i2'] = $aL['past_i2'] / count($past_i2);
            }
            if(count($past_i3) > 0) {
                foreach ($past_i3 as $ii3) {
                    $aL['past_i3'] += $ii3;
                }
                $aL['past_i3'] = $aL['past_i3'] / count($past_i3);
            }
            if(count($past_i4) > 0) {
                foreach ($past_i4 as $ii4) {
                    $aL['past_i4'] += $ii4;
                }
                $aL['past_i4'] = $aL['past_i4'] / count($past_i4);
            }
            if(count($past_i5) > 0) {
                foreach ($past_i5 as $ii5) {
                    $aL['past_i5'] += $ii5;
                }
                $aL['past_i5'] = $aL['past_i5'] / count($past_i5);
            }


            // удаляем незавершенный круг если он есть
            $last_key = count($aPastOrderCircles) - 1;
            if(!isset($aPastOrderCircles[$last_key]['end_order_time'])) {
                unset($aPastOrderCircles[$last_key]);
            }



            // считаем: среднее время между началами кругов
            $summ_circles_difference = [];
            $circles_differences_count = 0;
            $summ_circles_duration = 0; // считаем: среднее время круга
            $real_circles_count = 0; // будем искать отношение реально завершенных кругов к общему количеству


            foreach($aPastOrderCircles as $key => $aCircle) {
                // start_order_time, end_order_time, end_order_is_real
                $summ_circles_duration += $aCircle['end_order_time'] - $aCircle['start_order_time'];

                if($aCircle['end_order_is_real'] == true) {
                    $real_circles_count++;
                }

                if($key > 0) {
                    $summ_circles_difference[] = $aCircle['start_order_time'] - $aPastOrderCircles[$key - 1]['start_order_time'];
                    $circles_differences_count++;
                }
            }
//            echo 'client_id='.$client->id.'<br />';
//            echo "<pre>"; print_r($summ_circles_difference); echo "</pre>";
//            exit;
            //exit('summ_circles_difference='.$summ_circles_difference.' circles_differences_count='.$circles_differences_count);


            //$aL['past_i6'] = $summ_circles_difference/$circles_differences_count;
            if(count($aPastOrderCircles) > 0) {
                $aL['past_i7'] = $summ_circles_duration / count($aPastOrderCircles);
                $aL['past_i8'] = $real_circles_count / count($aPastOrderCircles);
            }


            if(count($present_i1) > 0) {
                foreach ($present_i1 as $ii1) {
                    $aL['present_i1'] += $ii1;
                }
                $aL['present_i1'] = $aL['present_i1'] / count($present_i1);
            }
            if(count($present_i2) > 0) {
                foreach ($present_i2 as $ii2) {
                    $aL['present_i2'] += $ii2;
                }
                $aL['present_i2'] = $aL['present_i2'] / count($present_i2);
            }
            if(count($present_i3) > 0) {
                foreach ($present_i3 as $ii3) {
                    $aL['present_i3'] += $ii3;
                }
                $aL['present_i3'] = $aL['present_i3'] / count($present_i3);
            }
            if(count($present_i4) > 0) {
                foreach ($present_i4 as $ii4) {
                    $aL['present_i4'] += $ii4;
                }
                $aL['present_i4'] = $aL['present_i4'] / count($present_i4);
            }
            if(count($present_i5) > 0) {
                foreach ($present_i5 as $ii5) {
                    $aL['present_i5'] += $ii5;
                }
                $aL['present_i5'] = $aL['present_i5'] / count($present_i5);
            }

            $aL['present_i6'] = 0;
            $aL['present_i7'] = 0;
            $aL['present_i8'] = 0;


            if(count($total_i1) > 0) {
                foreach ($total_i1 as $ii1) {
                    $aL['total_i1'] += $ii1;
                }
                $aL['total_i1'] = $aL['total_i1'] / count($total_i1);
            }
            if(count($total_i2) > 0) {
                foreach ($total_i2 as $ii2) {
                    $aL['total_i2'] += $ii2;
                }
                $aL['total_i2'] = $aL['total_i2'] / count($total_i2);
            }
            if(count($total_i3) > 0) {
                foreach ($total_i3 as $ii3) {
                    $aL['total_i3'] += $ii3;
                }
                $aL['total_i3'] = $aL['total_i3'] / count($total_i3);
            }
            if(count($total_i4) > 0) {
                foreach ($total_i4 as $ii4) {
                    $aL['total_i4'] += $ii4;
                }
                $aL['total_i4'] = $aL['total_i4'] / count($total_i4);
            }
            if(count($total_i5) > 0) {
                foreach ($total_i5 as $ii5) {
                    $aL['total_i5'] += $ii5;
                }
                $aL['total_i5'] = $aL['total_i5'] / count($total_i5);
            }

            $aL['total_i6'] = 0;
            $aL['total_i7'] = 0;
            $aL['total_i8'] = 0;


            $aLoyalities[] = $aL;
        }


        Yii::$app->db->createCommand()->batchInsert('loyality', array_keys($aFields), $aLoyalities)->execute();
    }
}
