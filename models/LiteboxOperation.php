<?php

namespace app\models;

use app\commands\ClientServerController;
use Yii;
use yii\base\ErrorException;


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

    // ! нужно будет перед выгрузкой тестовые значение заменить на реальные
    public static function makeOperationSell($order) {


//        две позиции по маске:
//        1. НПР ДАТА_РЕЙСА ВРПТ ВСЕГО_МЕСТ (СТ:1, ДЕТ:1, АЭР:1, ПРИЗ:1) - цена
//        2. ФИКС.ЦЕНА - цена
//
//        Пример для заказа без фикс.цены:
//        1. АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1) – 1850 руб
//        2. ФИКС. ТАРИФ – 0 руб.

//        $item1 = [
//            "name": "АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1)",
//            "price": 1,
//            "quantity": 2.0,
//            "sum": 2.0,
//            "vat":{
//                "type": "none",
//                "sum": 0.0
//            }
//        ];
//        $item2 = [
//            "name": "ФИКС. ТАРИФ",
//            "price": 0,
//            "quantity": 0.0,
//            "sum": 0.0,
//            "vat":{
//                "type": "none",
//                "sum": 0.0
//            }
//        ];

//        Пример для заказа с фикс.ценой:
//        1. КА 12.07.2019 13:00 5 мест (СТ:0, ДЕТ:1, АЭР:0, ПРИЗ:0) – 0 руб
//        2. ФИКС. ТАРИФ – 2500 руб.


        if($order->trip == null) {
            throw new ErrorException('Рейс не найден');
        }
        if($order->client == null) {
            throw new ErrorException('Клиент не найден');
        }
        if(empty($order->client->email)) {
            throw new ErrorException('У клиента не заполнена почта');
        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }

        // проверяем наличие копий
        $exist_litebox_operation = LiteboxOperation::find()->where(['order_id' => $order->id])->one();
        if($exist_litebox_operation != null) {
            throw new ErrorException('Операция "Приход" уже создавалась ранее');
        }

        $litebox_operation = new LiteboxOperation();
        $litebox_operation->order_id = $order->id;
        $litebox_operation->sell_at = time();
        if(!$litebox_operation->save(false)) {
            throw new ErrorException('Не удалось создать LiteboxOperation');
        }




        $total_price = 0;

        $airport_count_sent = 0;
        $yandexPointTo = $order->yandexPointTo;
        $yandexPointFrom = $order->yandexPointFrom;
        if(
            ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта
            $airport_count_sent++;
        }


        if($order->use_fix_price == true) {
//        Пример для заказа с фикс.ценой:
//        1. КА 12.07.2019 13:00 5 мест (СТ:0, ДЕТ:1, АЭР:0, ПРИЗ:0) – 0 руб
//        2. ФИКС. ТАРИФ – 2500 руб.

            $item1 = [
                'name' => 'тест - '.$order->direction->sh_name.' '.date("d.m.Y", $order->trip->date).' '.$order->trip->name.' (СТ:'.$order->student_count.', ДЕТ:'.$order->child_count.', АЭР:'.$airport_count_sent.', ПРИЗ:'.$order->prize_trip_count.') – 0 руб',
                'price' => 0, //intval($order->price),
                'quantity' => 0,
                'sum' => 0.00, //$order->price,
                //'measurement_unit' => "Услуга",
                'vat' => [ // налоги
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $item2 = [
                'name' => 'тест - '.'ФИКС. ТАРИФ – '.$order->price.' руб.',
                'price' => 1.00, //intval($order->price),
                'quantity' => 1,
                'sum' => 1.00, //$order->price,
                //'measurement_unit' => "Услуга",
                'vat' => [ // налоги
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $total_price = $order->price;


        }else {
//        две позиции по маске:
//        1. НПР ДАТА_РЕЙСА ВРПТ ВСЕГО_МЕСТ (СТ:1, ДЕТ:1, АЭР:1, ПРИЗ:1) - цена
//        2. ФИКС.ЦЕНА - цена
//
//        Пример для заказа без фикс.цены:
//        1. АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1) – 1850 руб
//        2. ФИКС. ТАРИФ – 0 руб.

            $item1 = [
                'name' => 'тест - '.$order->direction->sh_name.' '.date("d.m.Y", $order->trip->date).' '.$order->trip->name.' (СТ:'.$order->student_count.', ДЕТ:'.$order->child_count.', АЭР:'.$airport_count_sent.', ПРИЗ:'.$order->prize_trip_count.') – '.intval($order->price).' руб',
                'price' => 1, //intval($order->price),
                'quantity' => 1,
                'sum' => 1.00, // $order->price,
                //'measurement_unit' => "Услуга",
                'vat' => [
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $item2 = [
                'name' => 'тест - '.'ФИКС. ТАРИФ – 0 руб.',
                'price' => 0, //intval($order->price),
                'quantity' => 0,
                'sum' => 0.00, //$order->price,
                //'measurement_unit' => "Услуга",
                'vat' => [
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $total_price = $order->price;
        }

        $payments[0] = [
            'type' => 1,
            'sum' => 1.00, //$total_price
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
                'items' => [
                    $item1,
                    $item2,
                ],
                'payments' => $payments,
                'total' => 1.00, //$total_price,
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
//            [payload] =>
//            [error] => stdClass Object(
//                [text] => В чеке не заполнено обязательное поле: "Оплаты"(payments). Тип поля "payments" должен быть массив
//                [code] => 32
//                [type] => system
//                [uuid] => 8296393b-f2a7-47e7-83b7-c5fc1c7e0c83
//            )

            throw new ErrorException($result->error->text);

        }else {

//        stdClass Object
//        (
//            [status] => wait
//            [uuid] => ba11c632-0058-4fac-b412-8787622ba110
//            [error] =>
//        )

            $litebox_operation->sell_uuid = $result->uuid;
            $litebox_operation->sell_status = $result->status;
            $litebox_operation->sell_status_setting_time = time();
            if(!$litebox_operation->save(false)) {
                throw new ErrorException('Не удалось создать LiteboxOperation');
            }

            $order->setField('litebox_uuid', $result->uuid);
        }
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
        if(empty($order->client->email)) {
            throw new ErrorException('У клиента не заполнена почта');
        }
        if(empty($order->client->mobile_phone)) {
            throw new ErrorException('У клиента не заполнен мобильный телефон');
        }

        //$litebox_operation = new LiteboxOperation();
        $this->sell_refund_at = time();
        if(!$this->save(false)) {
            throw new ErrorException('Не удалось создать LiteboxOperation');
        }




        $total_price = 0;

        $airport_count_sent = 0;
        $yandexPointTo = $order->yandexPointTo;
        $yandexPointFrom = $order->yandexPointFrom;
        if(
            ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта
            $airport_count_sent++;
        }


        if($order->use_fix_price == true) {
//        Пример для заказа с фикс.ценой:
//        1. КА 12.07.2019 13:00 5 мест (СТ:0, ДЕТ:1, АЭР:0, ПРИЗ:0) – 0 руб
//        2. ФИКС. ТАРИФ – 2500 руб.

            $item1 = [
                'name' => 'тест - '.$order->direction->sh_name.' '.date("d.m.Y", $order->trip->date).' '.$order->trip->name.' (СТ:'.$order->student_count.', ДЕТ:'.$order->child_count.', АЭР:'.$airport_count_sent.', ПРИЗ:'.$order->prize_trip_count.') – 0 руб',
                'price' => 0, //intval($order->price),
                'quantity' => 0,
                'sum' => 0.00, //$order->price,
                'measurement_unit' => "Услуга",
                'vat' => [ // налоги
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $item2 = [
                'name' => 'тест - '.'ФИКС. ТАРИФ – '.$order->price.' руб.',
                'price' => 1.00, //intval($order->price),
                'quantity' => 1,
                'sum' => 1.00, //$order->price,
                'measurement_unit' => "Услуга",
                'vat' => [ // налоги
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $total_price = $order->price;


        }else {
//        две позиции по маске:
//        1. НПР ДАТА_РЕЙСА ВРПТ ВСЕГО_МЕСТ (СТ:1, ДЕТ:1, АЭР:1, ПРИЗ:1) - цена
//        2. ФИКС.ЦЕНА - цена
//
//        Пример для заказа без фикс.цены:
//        1. АК 19.07.2019 14:20 3 мест (СТ:0, ДЕТ:1, АЭР:3, ПРИЗ:1) – 1850 руб
//        2. ФИКС. ТАРИФ – 0 руб.

            $item1 = [
                'name' => 'тест - '.$order->direction->sh_name.' '.date("d.m.Y", $order->trip->date).' '.$order->trip->name.' (СТ:'.$order->student_count.', ДЕТ:'.$order->child_count.', АЭР:'.$airport_count_sent.', ПРИЗ:'.$order->prize_trip_count.') – '.intval($order->price).' руб',
                'price' => 1, //intval($order->price),
                'quantity' => 1,
                'sum' => 1.00, // $order->price,
                'measurement_unit' => "Услуга",
                'vat' => [
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $item2 = [
                'name' => 'тест - '.'ФИКС. ТАРИФ – 0 руб.',
                'price' => 0, //intval($order->price),
                'quantity' => 0,
                'sum' => 0.00, //$order->price,
                'measurement_unit' => "Услуга",
                'vat' => [
                    'type' => "none",
                    'sum' => 0.0
                ]
            ];

            $total_price = $order->price;
        }

        $payments[0] = [
            'type' => 1,
            'sum' => 1.00, //$total_price
        ];
        $data = [
            'external_id' => $this->sell_refund_at.'_'.$this->order_id , // 17052917561851307
            //'timestamp' => date("d.m.y H:i:s", $this->sell_refund_at),
            // нужен формат времени теперь: %d.%m.%Y %H:%M:%S
            'timestamp' => date("d.m.Y H:i:s", $this->sell_refund_at),
            'receipt' => [
                'client' => [
                    'email' => $order->client->email,
                    'phone' => $order->client->mobile_phone,
                ],
                'company' => [
                    'email' => "417417t@gmail.com",
                    'inn' => "165711720197",
                    'sno' => "envd",
                    'payment_address' => "t417.ru"
                ],
                'items' => [
                    $item1,
                    $item2,
                ],
                'payments' => $payments,
                'total' => 1.00, //$total_price,
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
        // echo "aResult:<pre>"; print_r($result); echo "</pre>";


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
                    $this->fiscal_document_number = $result->payload->fiscal_document_number;
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
                $this->litebox_ecr_registration_number = $this->ecr_registration_number;
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
                    $this->fiscal_document_number = $result->payload->fiscal_document_number;
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
