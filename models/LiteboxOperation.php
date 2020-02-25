<?php

namespace app\models;

use app\commands\ClientServerController;
use Yii;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;


class LiteboxOperation extends \yii\db\ActiveRecord
{
    public static $token = 'd8881c694429e766c7a36db089d1391148616178';
    public static $shop_id = 3563;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'litebox_operation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'sell_status_setting_time', 'sell_refund_status_setting_time', 'sell_at', 'sell_refund_at', ], 'integer'],
            [['sell_status', 'sell_refund_status'], 'string'],
            [['sell_uuid', 'sell_refund_uuid'], 'string', 'max' => 36],
            [['fn_number', 'ecr_registration_number'], 'string', 'max' => 16],
            [['fiscal_document_number', 'fiscal_document_attribute'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',

            'sell_uuid' => 'uuid операции "Приход", возвращенный сервером Litebox',
            'sell_at' => 'Время отправки запроса типа "Приход"',
            'sell_status' => 'Статус устанавливаемый при проведении операции "Приход"',
            'sell_status_setting_time' => 'Время установка статуса "Приход"',

            'sell_refund_uuid' => 'uuid операции "Возврат прихода", возвращенный сервером Litebox',
            'sell_refund_at' => 'Время отправки запроса типа "Возврат прихода"',
            'sell_refund_status' => 'Статус устанавливаемый при проведении операции "Возврат прихода"',
            'sell_refund_status_setting_time' => 'Время установка статуса "Возврат прихода"',

            'fn_number' => 'ФН номер (номер фискального накопителя)',
            'fiscal_document_number' => 'Номер_ФД',
            'ecr_registration_number' => 'Регистрационный номер ККТ (РНККТ)',
            'fiscal_document_attribute' => 'Фискальный признак достумента (ФПД)',
        ];
    }



    /**
     * проводим операцию "Приход"
     *
     * @param $order
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function makeOperationSell($order) {


        if($order->trip == null) {
            throw new ErrorException('Рейс не найден');
        }
        if($order->client == null) {
            throw new ErrorException('Клиент не найден');
        }
//        if(empty($order->client->email)) {
//            throw new ErrorException('У клиента не заполнена почта');
//        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }

        // нет мест - фискализацию не проводим
        if($order->is_not_places == true) {
            return true;
        }
        // для фикс.цены = 0
        if($order->use_fix_price == true && $order->price == 0) {
            return true;
        }


        // проверяем наличие копий
//        $exist_litebox_operation = LiteboxOperation::find()->where(['order_id' => $order->id])->one();
//        if($exist_litebox_operation != null) {
//            throw new ErrorException('Операция "Приход" уже создавалась ранее');
//        }

        $litebox_operation = new LiteboxOperation();
//        $litebox_operation->order_id = $order->id;
//        $litebox_operation->sell_at = time();
//        if(!$litebox_operation->save(false)) {
//            throw new ErrorException('Не удалось создать LiteboxOperation');
//        }





        $aItems = [];

        // is_not_places, places_count, student_count, child_count, prize_trip_count
        if($order->direction_id == 1) {
            $direction = 'Альметьевск-Казань';
        }else {
            $direction = 'Казань-Альметьевск';
        }


        $do_tariff = null;
        if($order->client_id > 0) {
            $do_tariff = $order->client->doTariff;
        }
        if($do_tariff == null) {
            $informer_office = $order->informerOffice;
            if ($informer_office != null) {
                $do_tariff = $informer_office->doTariff;
            }
        }




        $total_price = 0;
        if($order->use_fix_price == true || $do_tariff != null || Yii::$app->setting->loyalty_switch == 'cash_back_on') {

            if($order->places_count > 0) {

                //$summ = 1.00 * 1;
                $summ = $order->price;
                $total_price += $summ;

                $aItems[] = [
                    'name' => 'Заказная перевозка в нпр.' . $direction . ' по тарифу ИНД. (МЕСТ: '.$order->places_count.')',
                    'price' => 1.00, //intval($order->price),
                    'quantity' => 1,
                    'sum' => $summ, //$order->price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];
            }

        }else {


            $trip = $order->trip;
            if ($trip == null) {
                throw new ForbiddenHttpException('Рейс не найден');
            }
            $tariff = $trip->tariff;
            if ($tariff == null) {
                throw new ForbiddenHttpException('Тариф не найден');
            }

            $T_RESERV = $tariff->unprepayment_reservation_cost; // стоимость бронирования
            $T_COMMON = $tariff->unprepayment_common_price + $T_RESERV;  // цена по общему тарифу
            $T_STUDENT = $tariff->unprepayment_student_price + $T_RESERV; // студенческий тариф
            $T_BABY = $tariff->unprepayment_baby_price + $T_RESERV;    // детский тариф
            $T_AERO = $tariff->unprepayment_aero_price + $T_RESERV;    // тариф аэропорт
            $T_LOYAL = $tariff->unprepayment_loyal_price + $T_RESERV;   // тариф призовой поездки
            // $T_PARCEL = $tariff->unprepayment_parcel_price + $T_RESERV; // тариф отправки посылки (без места)



            $common_places = $order->places_count - $order->student_count - $order->child_count - $order->prize_trip_count;
            if($common_places > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_COMMON * ($common_places);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ОБЩ. (МЕСТ: '.$common_places.')',
                        'price' => $T_COMMON, //intval($order->price),
                        'quantity' => $common_places,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_COMMON * ($common_places);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ОБЩ. (МЕСТ: '.$common_places.')',
                        'price' => $T_COMMON, //intval($order->price),
                        'quantity' => $common_places,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }

            }

            if($order->student_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_STUDENT * ($order->student_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.СТУД. (МЕСТ: '.$order->student_count.')',
                        'price' => $T_STUDENT, //intval($order->price),
                        'quantity' => $order->student_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_STUDENT * ($order->student_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу СТУД. (МЕСТ: '.$order->student_count.')',
                        'price' => $T_STUDENT, //intval($order->price),
                        'quantity' => $order->student_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            if($order->child_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_BABY * ($order->child_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ДЕТ. (МЕСТ: '.$order->child_count.')',
                        'price' => $T_BABY, //intval($order->price),
                        'quantity' => $order->child_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_BABY * ($order->child_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ДЕТ. (МЕСТ: '.$order->child_count.')',
                        'price' => $T_BABY, //intval($order->price),
                        'quantity' => $order->child_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            if($order->prize_trip_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_LOYAL * ($order->prize_trip_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ПРИЗ. (МЕСТ: '.$order->prize_trip_count.')',
                        'price' => $T_LOYAL, //intval($order->price),
                        'quantity' => $order->prize_trip_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_LOYAL * ($order->prize_trip_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ПРИЗ. (МЕСТ: '.$order->prize_trip_count.')',
                        'price' => $T_LOYAL, //intval($order->price),
                        'quantity' => $order->prize_trip_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            // если клиенту едут в аэропорт, то они считаются по иной формуле
            $yandexPointTo = $order->yandexPointTo;
            $yandexPointFrom = $order->yandexPointFrom;
            if (
                ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
                || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
            ) { // едут в аэропорт или из аэропорта


                if($order->trip->commercial == 1) {

                    $summ = $T_AERO * ($order->places_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу АЭРОПОРТ. (МЕСТ: '.$order->places_count.')',
                        'price' => $T_AERO, //intval($order->price),
                        'quantity' => $order->places_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_AERO * ($order->places_count);
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.АЭРОПОРТ. (МЕСТ: '.$order->places_count.')',
                        'price' => $T_AERO, //intval($order->price),
                        'quantity' => $order->places_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

        }




        $payments[0] = [
            'type' => 1,
            'sum' => $total_price
        ];
        $data = [
            'external_id' => $litebox_operation->sell_at.'_'.$litebox_operation->order_id , // 17052917561851307
            //'timestamp' => date("d.m.y H:i:s", $litebox_operation->sell_at),
            // нужен формат времени теперь: %d.%m.%Y %H:%M:%S
            'timestamp' => date("d.m.Y H:i:s", $litebox_operation->sell_at),
            'receipt' => [
                'client' => [
                    //'email' => $order->client->email,
                    //'phone' => $order->client->mobile_phone,
                    //'phone' => '79661128006',
                    //'email' => '79661128006',

                    // в этом случае сообщение на почту не приходит, но приходит смс со ссылкой
                    'email' => $order->client->mobile_phone,
                ],
                'company' => [
                    'email' => "417417t@gmail.com",
                    'inn' => "165711720197",
                    'sno' => "envd",
                    'payment_address' => "t417.ru"
                ],
                'items' => $aItems,
                'payments' => $payments,
                'total' => $total_price,
                'is_print' => false
                //'is_print' => true
            ],
        ];

        echo "data:<pre>"; print_r($data); echo "</pre>";

        // пример:
        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178" "Accept:application/json" -H "Content-Type:application/json" -XPOST "https://in.litebox.ru/fiscalization/v1/shops/3563/sell" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{  \"external_id\": \"1558921554_1\",  \"timestamp\": \"12.07.17 22:00:00\",  \"receipt\": {    \"client\": {      \"email\": \"vlad.shetinin@gmail.com\"    },    \"company\": {      \"email\": \"417417t@gmail.com\",      \"inn\": \"165711720197\",      \"sno\": \"envd\",      \"payment_address\": \"t417.ru\"    },    \"items\": [      {        \"name\": \"АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)\",        \"price\": 1,        \"quantity\": 2.0,        \"sum\": 2.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      },       {        \"name\": \"ФИКС. ТАРИФ\",        \"price\": 0,        \"quantity\": 0.0,        \"sum\": 0.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      }    ],    \"payments\": [      {        \"type\": 1,        \"sum\": 2.0      }    ],    \"total\": 2.0,    \"is_print\": false  }}"

/*
        $headers[] = 'Authorization: Token '.self::$token;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $myCurl = curl_init();
        curl_setopt_array($myCurl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => 'https://in.litebox.ru/fiscalization/v1/shops/'.self::$shop_id.'/sell',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            //CURLOPT_POSTFIELDS => http_build_query($data)
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($myCurl);
        curl_close($myCurl);

        $result = json_decode($response);
        // echo "aResult:<pre>"; print_r($aResult); echo "</pre>";


        if(isset($result->error) && !empty($result->error)) {
            throw new ErrorException($result->error->text);

        }else {

            $litebox_operation->sell_uuid = $result->uuid;
            $litebox_operation->sell_status = $result->status;
            $litebox_operation->sell_status_setting_time = time();
            if(!$litebox_operation->save(false)) {
                throw new ErrorException('Не удалось создать LiteboxOperation');
            }

            $order->setField('litebox_uuid', $result->uuid);
        }*/
    }


    /**
     * На основе текущей операции создается новая операция отвены текущей операции
     * @throws ErrorException
     */
    public function makeOperationSellRefund() {

        $order = Order::find()->where(['id' => $this->order_id])->one();

        if($order == null) {
            throw new ErrorException('Заказ не найден');
        }
        if($order->client == null) {
            throw new ErrorException('Клиент не найден');
        }
//        if(empty($order->client->email)) {
//            throw new ErrorException('У клиента не заполнена почта');
//        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }

        //$litebox_operation = new LiteboxOperation();
        $this->sell_refund_at = time();
        if(!$this->save(false)) {
            throw new ErrorException('Не удалось создать LiteboxOperation');
        }



        // нет мест - фискализацию не проводим
        if($order->is_not_places == true) {
            return true;
        }
        // для фикс.цены = 0
        if($order->use_fix_price == true && $order->price == 0) {
            return true;
        }


        $aItems = [];

        // is_not_places, places_count, student_count, child_count, prize_trip_count
        if($order->direction_id == 1) {
            $direction = 'Альметьевск-Казань';
        }else {
            $direction = 'Казань-Альметьевск';
        }



        $do_tariff = null;
        if($order->client_id > 0) {
            $do_tariff = $order->client->doTariff;
        }
        if($do_tariff == null) {
            $informer_office = $order->informerOffice;
            if ($informer_office != null) {
                $do_tariff = $informer_office->doTariff;
            }
        }



        $total_price = 0;
        if($order->use_fix_price == 1 || $do_tariff != null || Yii::$app->setting->loyalty_switch == 'cash_back_on') {

            if($order->places_count > 0) {

                // $summ = 1.00 * 1;
                $summ = $order->price;
                $total_price += $summ;

                $aItems[] = [
                    'name' => 'Заказная перевозка в нпр.' . $direction . ' по тарифу ИНД. (МЕСТ: '.$order->places_count.')',
                    'price' => 1.00, //intval($order->price),
                    'quantity' => 1,
                    'sum' => $summ, //$order->price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];
            }

        }else {


            $trip = $order->trip;
            if ($trip == null) {
                throw new ForbiddenHttpException('Рейс не найден');
            }
            $tariff = $trip->tariff;
            if ($tariff == null) {
                throw new ForbiddenHttpException('Тариф не найден');
            }

            $T_RESERV = $tariff->unprepayment_reservation_cost; // стоимость бронирования
            $T_COMMON = $tariff->unprepayment_common_price + $T_RESERV;  // цена по общему тарифу
            $T_STUDENT = $tariff->unprepayment_student_price + $T_RESERV; // студенческий тариф
            $T_BABY = $tariff->unprepayment_baby_price + $T_RESERV;    // детский тариф
            $T_AERO = $tariff->unprepayment_aero_price + $T_RESERV;    // тариф аэропорт
            $T_LOYAL = $tariff->unprepayment_loyal_price + $T_RESERV;   // тариф призовой поездки
            // $T_PARCEL = $tariff->unprepayment_parcel_price + $T_RESERV; // тариф отправки посылки (без места)



            $common_places = $order->places_count - $order->student_count - $order->child_count - $order->prize_trip_count;
            if($common_places > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_COMMON * $common_places;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ОБЩ. (МЕСТ: '.$common_places.')',
                        'price' => $T_COMMON,
                        'quantity' => $common_places,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_COMMON * $common_places;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ОБЩ. (МЕСТ: '.$common_places.')',
                        'price' => $T_COMMON,
                        'quantity' => $common_places,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }

            }

            if($order->student_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_STUDENT * $order->student_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.СТУД. (МЕСТ: '.$order->student_count.')',
                        'price' => $T_STUDENT,
                        'quantity' => $order->student_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_STUDENT * $order->student_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу СТУД. (МЕСТ: '.$order->student_count.')',
                        'price' => $T_STUDENT,
                        'quantity' => $order->student_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            if($order->child_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_BABY * $order->child_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ДЕТ. (МЕСТ: '.$order->child_count.')',
                        'price' => $T_BABY,
                        'quantity' => $order->child_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];

                }else {

                    $summ = $T_BABY * $order->child_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ДЕТ. (МЕСТ: '.$order->child_count.')',
                        'price' => $T_BABY,
                        'quantity' => $order->child_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            if($order->prize_trip_count > 0) {

                if($order->trip->commercial == 1) {

                    $summ = $T_LOYAL * $order->prize_trip_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.ПРИЗ. (МЕСТ: '.$order->prize_trip_count.')',
                        'price' => $T_LOYAL,
                        'quantity' => $order->prize_trip_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];

                }else {

                    $summ = $T_LOYAL * $order->prize_trip_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу ПРИЗ. (МЕСТ: '.$order->prize_trip_count.')',
                        'price' => $T_LOYAL,
                        'quantity' => $order->prize_trip_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

            // если клиенту едут в аэропорт, то они считаются по иной формуле
            $yandexPointTo = $order->yandexPointTo;
            $yandexPointFrom = $order->yandexPointFrom;
            if (
                ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
                || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
            ) { // едут в аэропорт или из аэропорта


                if($order->trip->commercial == 1) {

                    $summ = $T_AERO * $order->places_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу АЭРОПОРТ. (МЕСТ: '.$order->places_count.')',
                        'price' => $T_AERO, //intval($order->price),
                        'quantity' => $order->places_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }else {

                    $summ = $T_AERO * $order->places_count;
                    $total_price += $summ;

                    $aItems[] = [
                        'name' => 'Заказная перевозка в нпр.'.$direction.' по тарифу КОММ.АЭРОПОРТ. (МЕСТ: '.$order->places_count.')',
                        'price' => $T_AERO, //intval($order->price),
                        'quantity' =>  $order->places_count,
                        'sum' => $summ, //$order->price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];
                }
            }

        }




        $payments[0] = [
            'type' => 1,
            'sum' => $total_price
        ];
        $data = [
            'external_id' => $this->sell_refund_at.'_'.$this->order_id , // 17052917561851307
            //'timestamp' => date("d.m.y H:i:s", $this->sell_refund_at),
            // нужен формат времени теперь: %d.%m.%Y %H:%M:%S
            'timestamp' => date("d.m.Y H:i:s", $this->sell_refund_at),
            'receipt' => [
                'client' => [
                    // 'email' => $order->client->email,
                    // 'phone' => $order->client->mobile_phone,

                    // в этом случае сообщение на почту не приходит, но приходит смс со ссылкой
                    'email' => $order->client->mobile_phone,
                ],
                'company' => [
                    'email' => "417417t@gmail.com",
                    'inn' => "165711720197",
                    'sno' => "envd",
                    'payment_address' => "t417.ru"
                ],
                'items' => $aItems,
                'payments' => $payments,
                'total' => $total_price,
                'is_print' => false
                //'is_print' => true
            ],
        ];

        // пример:
        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178" "Accept:application/json" -H "Content-Type:application/json" -XPOST "https://in.litebox.ru/fiscalization/v1/shops/3563/sell" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{  \"external_id\": \"1558921554_1\",  \"timestamp\": \"12.07.17 22:00:00\",  \"receipt\": {    \"client\": {      \"email\": \"vlad.shetinin@gmail.com\"    },    \"company\": {      \"email\": \"417417t@gmail.com\",      \"inn\": \"165711720197\",      \"sno\": \"envd\",      \"payment_address\": \"t417.ru\"    },    \"items\": [      {        \"name\": \"АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)\",        \"price\": 1,        \"quantity\": 2.0,        \"sum\": 2.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      },       {        \"name\": \"ФИКС. ТАРИФ\",        \"price\": 0,        \"quantity\": 0.0,        \"sum\": 0.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      }    ],    \"payments\": [      {        \"type\": 1,        \"sum\": 2.0      }    ],    \"total\": 2.0,    \"is_print\": false  }}"


        $headers[] = 'Authorization: Token '.self::$token;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $myCurl = curl_init();
        curl_setopt_array($myCurl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => 'https://in.litebox.ru/fiscalization/v1/shops/'.self::$shop_id.'/sell_refund',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($myCurl);
        curl_close($myCurl);
        $result = json_decode($response);
        // echo "aResult:<pre>"; print_r($aResult); echo "</pre>";


        if(isset($result->error) && !empty($result->error)) {
            throw new ErrorException($result->error->text);
        }else {
            $this->sell_refund_uuid = $result->uuid;
            $this->sell_refund_status = $result->status;
            $this->sell_refund_status_setting_time = time();
            if(!$this->save(false)) {
                throw new ErrorException('Не удалось сохранить LiteboxOperation');
            }

            $order->setField('litebox_uuid', $result->uuid);
        }
    }


    /**
     * @param bool $is_console
     * @return bool
     * @throws ErrorException
     */
    public function checkSellStatusAndUpdate($is_console = true) {

        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178"  -X GET "https://in.litebox.ru/fiscalization/v1/shops/3563/status/227dfc92-071a-439b-989a-c67baaf77877" -H  "accept: application/json"

        $headers[] = 'Authorization: Token '.self::$token;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178"  -X GET "https://in.litebox.ru/fiscalization/v1/shops/3563/status/c1355874-c253-443a-ba83-a5b03ada85fa" -H  "accept: application/json"
        $myCurl = curl_init();
        curl_setopt_array($myCurl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => 'https://in.litebox.ru/fiscalization/v1/shops/'.self::$shop_id.'/status/'.$this->sell_uuid,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($myCurl);
        curl_close($myCurl);

//        echo "sell_uuid=".$this->sell_uuid."<br />";
//        echo "response:<pre>"; print_r($response); echo "</pre>";
//        exit;

        $result = json_decode($response);
        echo "aResult:<pre>"; print_r($result); echo "</pre>";


        if(isset($result->error) && !empty($result->error)) {

            if($is_console == true) {
                ClientServerController::sendMessageToAdmin('Ошибка', 'sell_uuid: '.$this->sell_uuid.' '.$result->error->text);
            }else {
                throw new ErrorException($result->error->text);
            }

        }else {

//        ответ: {
//            "status":"done",
//            "device_code":"7e96f619576bdb19",
//            "uuid":"11b68179-52df-4433-8f26-04fa2010aa29",
//            "error":null,
//            "payload":{
//                "fiscal_receipt_number":1,
//                "fiscal_document_number":446,
//                "ecr_registration_number":"0003242520002452    ",
//                "receipt_id":"0f28a083-7d39-4b78-8514-415224a89504",
//                "shift_number":37,
//                "receipt_datetime":"15.07.2019 21:33:55",
//                "fiscal_document_attribute":1,
//                "fns_site":"https://consumer.ofd-gnivc.ru/",
//                "total":"2.00",
//                "fn_number":"9252440300032800",
//                "order_summ":0
//                }
//            }

            if($result->status == "done") {
                $this->sell_status = "done";
                $this->sell_status_setting_time = time();

                if($result->payload != null) {
                    $this->fn_number = $result->payload->fn_number;
                    $this->fiscal_document_number = intval($result->payload->fiscal_document_number) - 1; //  приходит на 1 больше чем должно :)
                    $this->ecr_registration_number = $result->payload->ecr_registration_number;
                    $this->fiscal_document_attribute = $result->payload->fiscal_document_attribute;
                }

            }elseif($result->status == "fail") {
                $this->sell_status = "fail";
                $this->sell_status_setting_time = time();
            }

            if(!$this->save(false)) {
                if($is_console == true) {
                    ClientServerController::sendMessageToAdmin('Ошибка', 'LiteboxOperation::checkStatusAndUpdate Не удалось сохранить данные по litebox операции');
                }else {
                    throw new ErrorException('Не удалось сохранить данные по litebox операции');
                }
            }

            if(!empty($this->fn_number)) {

                $order = Order::find()->where(['id' => $this->order_id])->one();
                $order->litebox_fn_number = $this->fn_number;
                $order->litebox_fiscal_document_number = $this->fiscal_document_number;
                $order->litebox_fiscal_document_attribute = $this->fiscal_document_attribute;
                $order->litebox_ecr_registration_number = $this->ecr_registration_number;
                if (!$order->save(false)) {
                    if ($is_console == true) {
                        ClientServerController::sendMessageToAdmin('Ошибка', 'LiteboxOperation::checkStatusAndUpdate Не удалось сохранить данные по заказу');
                    } else {
                        throw new ErrorException('Не удалось сохранить данные по заказу');
                    }
                }
            }
        }

        return true;
    }



    /**
     * @param bool $is_console
     * @return bool
     * @throws ErrorException
     */
    public function checkSellRefundStatusAndUpdate($is_console = true) {

        $headers[] = 'Authorization: Token '.self::$token;
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $myCurl = curl_init();
        curl_setopt_array($myCurl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => 'https://in.litebox.ru/fiscalization/v1/shops/'.self::$shop_id.'/status/'.$this->sell_uuid,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($myCurl);
        curl_close($myCurl);

//        echo "sell_uuid=".$this->sell_uuid."<br />";
//        echo "response:<pre>"; print_r($response); echo "</pre>";
//        exit;

        $result = json_decode($response);
        // echo "aResult:<pre>"; print_r($aResult); echo "</pre>";


        if(isset($result->error) && !empty($result->error)) {

            if($is_console == true) {
                ClientServerController::sendMessageToAdmin('Ошибка', 'sell_uuid: '.$this->sell_uuid.' '.$result->error->text);
            }else {
                throw new ErrorException($result->error->text);
            }

        }else {

//        ответ: {
//            {
//                "status":"done",
//                "device_code":"7e96f619576bdb19",
//                "uuid":"5b2dc12e-52be-464d-a8b0-deeb397d7984",
//                "error":null,
//                "payload":{
//                    "fiscal_receipt_number":32,
//                    "fiscal_document_number":571,
//                    "ecr_registration_number":"0003242520002452    ",
//                    "receipt_id":"c6aac189-55dc-4a33-8029-0f0205e61c44",
//                    "shift_number":41,
//                    "receipt_datetime":"19.07.2019 17:34:18",
//                    "fiscal_document_attribute":1,
//                    "fns_site":"https://consumer.ofd-gnivc.ru/",
//                    "total":"1.00",
//                    "fn_number":"9252440300032800",
//                    "order_summ":0
//                }
//            }

            if($result->status == "done") {
                $this->sell_refund_status = "done";
                $this->sell_refund_status_setting_time = time();

                if($result->payload != null) {
                    $this->fn_number = $result->payload->fn_number;
                    $this->fiscal_document_number = intval($result->payload->fiscal_document_number) - 1; //  приходит на 1 больше чем должно :)
                    $this->ecr_registration_number = $result->payload->ecr_registration_number;
                    $this->fiscal_document_attribute = $result->payload->fiscal_document_attribute;
                }

            }elseif($result->status == "fail") {
                $this->sell_refund_status = "fail";
                $this->sell_refund_status_setting_time = time();
            }

            if(!$this->save(false)) {
                if($is_console == true) {
                    ClientServerController::sendMessageToAdmin('Ошибка', 'LiteboxOperation::checkStatusAndUpdate Не удалось сохранить данные по litebox операции');
                }else {
                    throw new ErrorException('Не удалось сохранить данные по litebox операции');
                }
            }

            if(!empty($this->fn_number)) {

                $order = Order::find()->where(['id' => $this->order_id])->one();
                $order->litebox_fn_number = $this->fn_number;
                $order->litebox_fiscal_document_number = $this->fiscal_document_number;
                $order->litebox_ecr_registration_number = $this->ecr_registration_number;
                $order->litebox_fiscal_document_attribute = $this->fiscal_document_attribute;
                if (!$order->save(false)) {
                    if ($is_console == true) {
                        ClientServerController::sendMessageToAdmin('Ошибка', 'LiteboxOperation::checkStatusAndUpdate Не удалось сохранить данные по заказу');
                    } else {
                        throw new ErrorException('Не удалось сохранить данные по заказу');
                    }
                }
            }
        }

        return true;
    }
}
