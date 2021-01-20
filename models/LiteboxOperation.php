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
            [['order_id', 'sell_status_setting_time', 'sell_refund_status_setting_time', 'sell_at',
                'sell_refund_at', 'commercial_trip', 'direction_id'], 'integer'],
            [['sell_status', 'sell_refund_status', 'place_type'], 'string'],
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
            'commercial_trip' => 'Коммерческий рейс',
            'direction_id' => 'Направление',
            'place_type' => 'Тип места',
            'place_price' => 'Цена за место',

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
    public static function makeOperationSellOld($order, $litebox_operation = null) {

        // вместо uuid временно верну id последней записи  litebox_operation
//        $last_litebox_operation = LiteboxOperation::find()->orderBy(['id' => 'SORT_DESC'])->one();
//        return $last_litebox_operation->id;
//
//        return $result->uuid;

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



        if($litebox_operation != null) {

            // повторный запрос той же операции в litebox (производиться если на первый запрос litebox не ответил на запрос)
            $litebox_operation->order_id = $order->id;
            $litebox_operation->sell_at = time();
            if (!$litebox_operation->save(false)) {
                throw new ErrorException('Не удалось обновить LiteboxOperation');
            }
        }else {

            // проверяем наличие неотменненных копий
            $exist_litebox_operation = LiteboxOperation::find()
                ->where(['order_id' => $order->id])
                ->andWhere(['sell_refund_status' => NULL])
                ->one();
            if($exist_litebox_operation != null) {
                throw new ErrorException('Операция "Приход" уже создавалась ранее');
            }

            $litebox_operation = new LiteboxOperation();
            $litebox_operation->order_id = $order->id;
            $litebox_operation->sell_at = time();
            if (!$litebox_operation->save(false)) {
                throw new ErrorException('Не удалось создать LiteboxOperation');
            }
        }





        $aItems = [];

        /*
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

                $summ = $order->price;
                $total_price += $summ;

                $aItems[] = [
                    'name' => 'Заказная перевозка в нпр.' . $direction . ' по тарифу ИНД. (МЕСТ: '.$order->places_count.')',
                    'price' => $total_price, //intval($order->price),
                    'quantity' => 1,
                    'sum' => $total_price, //$order->price,
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


                if($yandexPointTo != null && $yandexPointTo->alias == 'airport') {

                    if($order->direction_id == 1) {
                        $direction = 'Альметьевск Казань-Аэропорт';
                    }else {
                        $direction = 'Казань Альметьевск-Аэропорт';
                    }

                }else {

                    if($order->direction_id == 1) {
                        $direction = 'Альметьевск-Аэропорт Казань';
                    }else {
                        $direction = 'Казань-Аэропорт Альметьевск';
                    }
                }


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

            }else {

                if($order->trip->commercial == 1) {

                    $aItems[] = [
                        'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (коммерческий) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                        'price' => $total_price,
                        'quantity' => 1,
                        'sum' => $total_price,
                        'vat' => [ // налоги
                            'type' => "none",
                            'sum' => 0.0
                        ],
                        'payment_object' => 'service',
                        'payment_method' => 'full_payment',
                        'measurement_unit' => 'мест'
                    ];

                }else {

                    $aItems[] = [
                        'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (стандарт) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                        'price' => $total_price,
                        'quantity' => 1,
                        'sum' => $total_price,
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

        }*/


        $total_price = intval($order->price);

        // если клиенту едут в аэропорт, то они считаются по иной формуле
        $yandexPointTo = $order->yandexPointTo;
        $yandexPointFrom = $order->yandexPointFrom;
        if (
            ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта


            if($yandexPointTo != null && $yandexPointTo->alias == 'airport') {

                if($order->direction_id == 1) {
                    $direction = 'Альметьевск-Аэропорт Казань';
                }else {
                    $direction = 'Казань-Альметьевск';
                }

            }else {

                if($order->direction_id == 1) {
                    $direction = 'Альметьевск-Казань';
                }else {
                    $direction = 'Аэропорт Казань-Альметьевск';
                }
            }


            if($order->trip->commercial == 1) {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (коммерческий) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф КОМ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];

            }else {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (стандарт) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф ОБЩ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
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

            if($order->direction_id == 1) {
                $direction = 'Альметьевск-Казань';
            }else {
                $direction = 'Казань-Альметьевск';
            }

            if($order->trip->commercial == 1) {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (коммерческий) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф КОМ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];

            }else {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (стандарт) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф ОБЩ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
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




        $payments[0] = [
            'type' => 1,
            'sum' => $total_price
        ];
        $data = [
            'external_id' => $litebox_operation->id.'_'.$litebox_operation->order_id.'_'.$litebox_operation->sell_at,
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
                    //'sno' => "envd",
                    'sno' => "patent",
                    'payment_address' => "t417.ru"
                ],
                'items' => $aItems,
                'payments' => $payments,
                'total' => $total_price,
                'is_print' => false
                //'is_print' => true
            ],
        ];

        //echo "data:<pre>"; print_r($data); echo "</pre>";

        // пример:
        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178" "Accept:application/json" -H "Content-Type:application/json" -XPOST "https://in.litebox.ru/fiscalization/v1/shops/3563/sell" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{  \"external_id\": \"1558921554_1\",  \"timestamp\": \"12.07.17 22:00:00\",  \"receipt\": {    \"client\": {      \"email\": \"vlad.shetinin@gmail.com\"    },    \"company\": {      \"email\": \"417417t@gmail.com\",      \"inn\": \"165711720197\",      \"sno\": \"envd\",      \"payment_address\": \"t417.ru\"    },    \"items\": [      {        \"name\": \"АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)\",        \"price\": 1,        \"quantity\": 2.0,        \"sum\": 2.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      },       {        \"name\": \"ФИКС. ТАРИФ\",        \"price\": 0,        \"quantity\": 0.0,        \"sum\": 0.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      }    ],    \"payments\": [      {        \"type\": 1,        \"sum\": 2.0      }    ],    \"total\": 2.0,    \"is_print\": false  }}"


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

            return $result->uuid;
        }
    }


    /*
     * Для всех мест в заказе создаются записи в таблице litebox_operation и запрашивается печать чеков
     */
    public static function makeOperationSell($order, $throw_errors) {

        if($order->trip == null) {
            throw new ErrorException('Рейс не найден');
        }
        $tariff = $order->trip->tariff;
        if ($tariff == null) {
            throw new ErrorException('Тариф не найден');
        }
        if($order->client == null) {
            throw new ErrorException('Клиент не найден');
        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }
        if(!empty($order->paid_time)) {
            throw new ForbiddenHttpException('По заказу уже производился платеж');
        }

        // нет мест - фискализацию не проводим
        if($order->is_not_places == true) {
            if($throw_errors) {
                throw new ErrorException('Нет мест в заказе, фискализация запрещена');
            }else {
                return false;
            }
        }
        // для фикс.цены = 0
        if($order->use_fix_price == true && $order->price == 0) {
            if($throw_errors) {
                throw new ErrorException('Для заказа с фикс.ценой равной 0 фискализация запрещена');
            }else {
                return false;
            }
        }



        // проверка уже существующих запрос в litebox для этого заказа
        $litebox_operation = LiteboxOperation::find()
            ->where(['order_id' => $order->id])
            ->andWhere(['sell_refund_uuid' => NULL])
            ->one();
        if($litebox_operation != null) {
            if($throw_errors) {
                throw new ErrorException('Операция на создание чеков для этого заказа уже была создана');
            }else {
                return false;
            }
        }

        $T_RESERV = $tariff->unprepayment_reservation_cost; // стоимость бронирования
        $T_COMMON = $tariff->unprepayment_common_price + $T_RESERV;  // цена по общему тарифу
        $T_STUDENT = $tariff->unprepayment_student_price + $T_RESERV; // студенческий тариф
        $T_BABY = $tariff->unprepayment_baby_price + $T_RESERV;    // детский тариф
        $T_AERO = $tariff->unprepayment_aero_price + $T_RESERV;    // тариф аэропорт
        //$T_LOYAL = $tariff->unprepayment_loyal_price + $T_RESERV;   // тариф призовой поездки
        //$T_PARCEL = $tariff->unprepayment_parcel_price + $T_RESERV; // тариф отправки посылки (без места)



        $aLiteboxes = [];


        if($order->use_fix_price == 1) {

            $litebox = new LiteboxOperation();
            $litebox->order_id = $order->id;
            $litebox->commercial_trip = $order->trip->commercial;
            $litebox->direction_id = $order->direction_id;
            $litebox->place_type = 'fix_price'; // 'fix_price','airport','adult','student','child',''
            $litebox->place_price = $order->price;
            $litebox->save();

            $aLiteboxes[] = $litebox;

        }else {

            $total_count = intval($order->places_count); // количество мест в текущем заказе
            if($total_count <= 0) {
                throw new ErrorException('Нет мест в заказе');
            }

            $student_count = intval($order->student_count); // количество студентов в текущем заказе
            $child_count = intval($order->child_count); // количество детей в текущем заказе
            $adult_count = $total_count - $student_count - $child_count; // общие места
            if($adult_count < 0) {
                throw new ErrorException('Колличество мест для взрослых меньше 0');
            }


            $yandexPointTo = $order->yandexPointTo;
            $yandexPointFrom = $order->yandexPointFrom;
            if (
                ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
                || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
            ) { // едут в аэропорт или из аэропорта

                for($i = 0; $i < $total_count; $i++) {

                    $litebox = new LiteboxOperation();
                    $litebox->order_id = $order->id;
                    $litebox->commercial_trip = $order->trip->commercial;
                    $litebox->direction_id = $order->direction_id;
                    $litebox->place_type = 'airport'; // 'fix_price','airport','adult','student','child',''
                    $litebox->place_price = $T_AERO;
                    $litebox->save();

                    $aLiteboxes[] = $litebox;
                }

            }else {

                if($adult_count > 0) {
                    for($i = 0; $i < $adult_count; $i++) {

                        $litebox = new LiteboxOperation();
                        $litebox->order_id = $order->id;
                        $litebox->commercial_trip = $order->trip->commercial;
                        $litebox->direction_id = $order->direction_id;
                        $litebox->place_type = 'adult'; // 'fix_price','airport','adult','student','child',''
                        $litebox->place_price = $T_COMMON;
                        $litebox->save();

                        $aLiteboxes[] = $litebox;
                    }
                }

                if($student_count > 0) {
                    for($i = 0; $i < $student_count; $i++) {

                        $litebox = new LiteboxOperation();
                        $litebox->order_id = $order->id;
                        $litebox->commercial_trip = $order->trip->commercial;
                        $litebox->direction_id = $order->direction_id;
                        $litebox->place_type = 'student'; // 'fix_price','airport','adult','student','child',''
                        $litebox->place_price = $T_STUDENT;
                        $litebox->save();

                        $aLiteboxes[] = $litebox;
                    }
                }

                if($child_count > 0) {
                    for($i = 0; $i < $child_count; $i++) {

                        $litebox = new LiteboxOperation();
                        $litebox->order_id = $order->id;
                        $litebox->commercial_trip = $order->trip->commercial;
                        $litebox->direction_id = $order->direction_id;
                        $litebox->place_type = 'child'; // 'fix_price','airport','adult','student','child',''
                        $litebox->place_price = $T_BABY;
                        $litebox->save();

                        $aLiteboxes[] = $litebox;
                    }
                }
            }
        }


        if(count($aLiteboxes) == 0) {
            throw new ErrorException('Не обнаружены данные для печати');
        }



        foreach ($aLiteboxes as $litebox)
        {
            $litebox->sell_at = time();
            $litebox->save(false);

            if($litebox->direction_id == 1) {
                $direction = 'Альметьевск-Казань';
            }else {
                $direction = 'Казань-Альметьевск';
            }

            if($litebox->commercial_trip == 1) {
                $kommer_reis = '+КР.';
            }else {
                $kommer_reis = '';
            }

            $name = '';
            switch($litebox->place_type)
            {
                case 'fix_price':
                    $name = 'Заказная перевозка пассажиров - '.$litebox->place_price.'х1 = '.$litebox->place_price.' руб';
                    break;

                case 'airport':
                    $place_type = 'ПОВ';
                    $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$litebox->place_price.' руб';
                    break;

                case 'adult':
                    $place_type = 'ОБЩ';
                    $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$litebox->place_price.' руб';
                    break;

                case 'student':
                    $place_type = 'СТУД';
                    $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$litebox->place_price.' руб';
                    break;

                case 'child':
                    $place_type = 'ДЕТ';
                    $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$litebox->place_price.' руб';
                    break;

                default:
                    throw new ErrorException('Не определен тип места');
                    break;
            }

            $aItems = [];
            $aItems[] = [
                'name' => $name,
                'price' => $litebox->place_price,
                'quantity' => 1,
                'sum' => $litebox->place_price,
                'vat' => [ // налоги
                    'type' => "none",
                    'sum' => 0.0
                ],
                'payment_object' => 'service',
                'payment_method' => 'full_payment',
                'measurement_unit' => 'мест'
            ];

            $payments[0] = [
                'type' => 1,
                'sum' => $litebox->place_price
            ];
            $data = [
                'external_id' => $litebox->id.'_'.$litebox->order_id.'_'.$litebox->sell_at,
                'timestamp' => date("d.m.Y H:i:s", $litebox->sell_at),
                'receipt' => [
                    'client' => [
                        //'email' => $order->client->email,
                        //'phone' => $order->client->mobile_phone,
                        //'phone' => '79661128006',
                        //'email' => '79661128006',
                        'email' => $order->client->mobile_phone,// в этом случае сообщение на почту не приходит, но приходит смс со ссылкой
                    ],
                    'company' => [
                        'email' => "417417t@gmail.com",
                        'inn' => "165711720197",
                        'sno' => "patent",
                        'payment_address' => "t417.ru"
                    ],
                    'items' => $aItems,
                    'payments' => $payments,
                    'total' => $litebox->place_price,
                    'is_print' => false
                    //'is_print' => true
                ],
            ];


            // пример:
            // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178" "Accept:application/json" -H "Content-Type:application/json" -XPOST "https://in.litebox.ru/fiscalization/v1/shops/3563/sell" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{  \"external_id\": \"1558921554_1\",  \"timestamp\": \"12.07.17 22:00:00\",  \"receipt\": {    \"client\": {      \"email\": \"vlad.shetinin@gmail.com\"    },    \"company\": {      \"email\": \"417417t@gmail.com\",      \"inn\": \"165711720197\",      \"sno\": \"envd\",      \"payment_address\": \"t417.ru\"    },    \"items\": [      {        \"name\": \"АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)\",        \"price\": 1,        \"quantity\": 2.0,        \"sum\": 2.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      },       {        \"name\": \"ФИКС. ТАРИФ\",        \"price\": 0,        \"quantity\": 0.0,        \"sum\": 0.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      }    ],    \"payments\": [      {        \"type\": 1,        \"sum\": 2.0      }    ],    \"total\": 2.0,    \"is_print\": false  }}"
            $headers = [];
            $headers[] = 'Authorization: Token '.self::$token;
            $headers[] = 'Content-Type: application/json; charset=UTF-8';

            $myCurl = curl_init();
            curl_setopt_array($myCurl, [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_URL => 'https://in.litebox.ru/fiscalization/v1/shops/'.self::$shop_id.'/sell',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data)
            ]);
            $response = curl_exec($myCurl);
            curl_close($myCurl);
            $result = json_decode($response);


            if(isset($result->error) && !empty($result->error)) {
                throw new ErrorException($result->error->text);
            }else {

                $litebox->sell_uuid = $result->uuid;
                $litebox->sell_status = $result->status;
                $litebox->sell_status_setting_time = time();
                if(!$litebox->save(false)) {
                    throw new ErrorException('Не удалось создать LiteboxOperation');
                }

            }

            //sleep(1);
        }

        // $order->setField('litebox_completed', true); // изменения в заказе вынесены из этой функции

        return true;
    }


    /**
     * На основе текущей операции создается новая операция отвены текущей операции
     * @throws ErrorException
     */
    public function makeOperationSellRefundOld() {

        // вместо uuid временно верну id последней записи  litebox_operation
//        $last_litebox_operation = LiteboxOperation::find()->orderBy(['id' => 'SORT_DESC'])->one();
//        return $last_litebox_operation->id;

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

        /*
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
                    'price' => $total_price, //intval($order->price),
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

        }*/


        $total_price = intval($order->price);

        // если клиенту едут в аэропорт, то они считаются по иной формуле
        $yandexPointTo = $order->yandexPointTo;
        $yandexPointFrom = $order->yandexPointFrom;
        if (
            ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта

            if($yandexPointTo != null && $yandexPointTo->alias == 'airport') {

                if($order->direction_id == 1) {
                    $direction = 'Альметьевск-Аэропорт Казань';
                }else {
                    $direction = 'Казань-Альметьевск';
                }

            }else {

                if($order->direction_id == 1) {
                    $direction = 'Альметьевск-Казань';
                }else {
                    $direction = 'Аэропорт Казань-Альметьевск';
                }
            }


            if($order->trip->commercial == 1) {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (коммерческий) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф КОМ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];

            }else {

                $aItems[] = [
                    // 'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (стандарт) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф ОБЩ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
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

            if($order->direction_id == 1) {
                $direction = 'Альметьевск-Казань';
            }else {
                $direction = 'Казань-Альметьевск';
            }

            if($order->trip->commercial == 1) {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (коммерческий) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф КОМ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
                    'vat' => [ // налоги
                        'type' => "none",
                        'sum' => 0.0
                    ],
                    'payment_object' => 'service',
                    'payment_method' => 'full_payment',
                    'measurement_unit' => 'мест'
                ];

            }else {

                $aItems[] = [
                    //'name' => 'ТЕСТИРОВАНИЕ: УСЛУГА заказной перевозки по нпр.' . $direction . ' ' . date('d.m.Y', $order->date) . ' (стандарт) ' . $order->places_count . ' МЕСТ, в том числе ' . intval($order->student_count) . ' СТ, ' . intval($order->child_count) . ' ДЕТ, ' . intval($order->prize_trip_count) . ' ПРИЗ',
                    'name' => 'Заказная перевозка '.$direction.' (тариф ОБЩ) '.date('d.m.Y', $order->date).', МЕСТ:'.$order->places_count,
                    'price' => $total_price,
                    'quantity' => 1,
                    'sum' => $total_price,
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


        $payments[0] = [
            'type' => 1,
            'sum' => $total_price
        ];
        $data = [
            'external_id' => $this->id.'_'.$this->order_id.'_'.$this->sell_refund_at,
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
                    //'sno' => "envd",
                    'sno' => "patent",
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
        //echo "data:<pre>"; print_r($data); echo "</pre>";
        //exit;

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

    /*
     * Запрашивается отмена текущей litebox_operation с выдачей чека
     */
    public function makeOperationSellRefund()
    {
        $order = Order::find()
            ->where(['id' => $this->order_id])
            ->limit(1)
            ->one();

        if($order == null) {
            throw new ErrorException('Заказ не найден');
        }
        if($order->client == null) {
            throw new ErrorException('Клиент не найден');
        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }

        $this->sell_refund_at = time();
        if(!$this->save(false)) {
            throw new ErrorException('Не удалось создать LiteboxOperation');
        }


        if($this->direction_id == 1) {
            $direction = 'Альметьевск-Казань';
        }else {
            $direction = 'Казань-Альметьевск';
        }

        if($this->commercial_trip == 1) {
            $kommer_reis = '+КР.';
        }else {
            $kommer_reis = '';
        }

        $name = '';
        switch($this->place_type)
        {
            case 'fix_price':
                $name = 'Заказная перевозка пассажиров - '.$this->place_price.'х1 = '.$this->place_price.' руб';
                break;

            case 'airport':
                $place_type = 'ПОВ';
                $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$this->place_price.' руб';
                break;

            case 'adult':
                $place_type = 'ОБЩ';
                $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$this->place_price.' руб';
                break;

            case 'student':
                $place_type = 'СТУД';
                $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$this->place_price.' руб';
                break;

            case 'child':
                $place_type = 'ДЕТ';
                $name = 'Заказная перевозка '.$direction.' (тариф '.$place_type.''.$kommer_reis.'), МЕСТ:1 - '.$this->place_price.' руб';
                break;

            default:
                throw new ErrorException('Не определен тип места');
                break;
        }

        $aItems = [];
        $aItems[] = [
            'name' => $name,
            'price' => $this->place_price,
            'quantity' => 1,
            'sum' => $this->place_price,
            'vat' => [ // налоги
                'type' => "none",
                'sum' => 0.0
            ],
            'payment_object' => 'service',
            'payment_method' => 'full_payment',
            'measurement_unit' => 'мест'
        ];



        $payments[0] = [
            'type' => 1,
            'sum' => $this->place_price
        ];
        $data = [
            'external_id' => $this->id.'_'.$this->order_id.'_'.$this->sell_refund_at,
            'timestamp' => date("d.m.Y H:i:s", $this->sell_refund_at),
            'receipt' => [
                'client' => [
                    // в этом случае сообщение на почту не приходит, но приходит смс со ссылкой
                    'email' => $order->client->mobile_phone,
                ],
                'company' => [
                    'email' => "417417t@gmail.com",
                    'inn' => "165711720197",
                    'sno' => "patent",
                    'payment_address' => "t417.ru"
                ],
                'items' => $aItems,
                'payments' => $payments,
                'total' => $this->place_price,
                'is_print' => false
                //'is_print' => true
            ],
        ];

        // пример:
        // curl -i -H "Authorization: Token d8881c694429e766c7a36db089d1391148616178" "Accept:application/json" -H "Content-Type:application/json" -XPOST "https://in.litebox.ru/fiscalization/v1/shops/3563/sell" -H  "accept: application/json" -H  "Content-Type: application/json" -d "{  \"external_id\": \"1558921554_1\",  \"timestamp\": \"12.07.17 22:00:00\",  \"receipt\": {    \"client\": {      \"email\": \"vlad.shetinin@gmail.com\"    },    \"company\": {      \"email\": \"417417t@gmail.com\",      \"inn\": \"165711720197\",      \"sno\": \"envd\",      \"payment_address\": \"t417.ru\"    },    \"items\": [      {        \"name\": \"АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)\",        \"price\": 1,        \"quantity\": 2.0,        \"sum\": 2.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      },       {        \"name\": \"ФИКС. ТАРИФ\",        \"price\": 0,        \"quantity\": 0.0,        \"sum\": 0.0,        \"vat\":{            \"type\": \"none\",            \"sum\": 0.0        }      }    ],    \"payments\": [      {        \"type\": 1,        \"sum\": 2.0      }    ],    \"total\": 2.0,    \"is_print\": false  }}"
        $headers = [];
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
        }


        return true;
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
        $result = json_decode($response);


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
                    $this->fiscal_document_number = intval($result->payload->fiscal_document_number);
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

//            if(!empty($this->fn_number)) {
//
//                $order = Order::find()->where(['id' => $this->order_id])->one();
//
//                $order->setField('litebox_fn_number', $this->fn_number);
//                $order->setField('litebox_fiscal_document_number', $this->fiscal_document_number);
//                $order->setField('litebox_fiscal_document_attribute', $this->fiscal_document_attribute);
//                $order->setField('litebox_ecr_registration_number', $this->ecr_registration_number);
//            }
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
        $result = json_decode($response);


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
                    $this->fiscal_document_number = intval($result->payload->fiscal_document_number);
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

//            if(!empty($this->fn_number)) {
//
//                $order = Order::find()->where(['id' => $this->order_id])->one();
//
//                $order->setField('litebox_uuid', NULL);
//                $order->setField('litebox_fn_number', NULL);
//                $order->setField('litebox_fiscal_document_number', NULL);
//                $order->setField('litebox_fiscal_document_attribute', NULL);
//                $order->setField('litebox_ecr_registration_number', NULL);
//            }
        }

        return true;
    }
}
