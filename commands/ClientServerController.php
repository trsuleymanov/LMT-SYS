<?php

namespace app\commands;

use app\models\City;
use app\models\Client;
use app\models\ClientServer;
use app\models\Direction;
use app\models\DispatcherAccounting;
use app\models\InformerOffice;
use app\models\LiteboxOperation;
use app\models\Order;
use app\models\Passenger;
use app\models\Trip;
use app\models\YandexPoint;
use app\widgets\IncomingOrdersWidget;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;


/**
 *  Отсюда отправляются запросы в клиентский сервер (запросы-экшены вызываются кроном)
 */
class ClientServerController extends Controller
{
    /*
     * Чтение изменений по пользователям-клиентам с клиентского сервера
     * php yii client-server/get-users
     */
    public function actionGetUsers() {

        $request_1 = new \yii\httpclient\Client(); // это клиент запроса, а не Клиент-человек

        $response = $request_1->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['clientServerUrl'].'user/get-users')
            ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
            ->send();
        if ($response->statusCode == 200) {

            $aIds = [];
            $count_new = 0;
            $count_old = 0;
            $aUsers = $response->data;
            if(count($aUsers) > 0) {
                foreach($aUsers as $aUser) {
                    $client = null;
                    if(!empty($aUser['email'])) {
                        $client = \app\models\Client::find()
                            ->where(['email' => $aUser['email']])
                            ->one();
                    }
                    if(!empty($aUser['phone']) && $client == null) {
                        $client = \app\models\Client::find()
                            ->where(['mobile_phone' => $aUser['phone']])
                            ->one();
                    }

                    if($client == null) {
                        $client = new \app\models\Client();
                        $client->email = $aUser['email'];
                        $client->name = $aUser['fio'];
                        $client->mobile_phone = $aUser['phone'];
                        $client->cashback = $aUser['cashback'];
                        $client->current_year_sended_places = $aUser['current_year_sended_places'];
                        $client->current_year_sended_prize_places = $aUser['current_year_sended_prize_places'];
                        $client->current_year_penalty = $aUser['current_year_penalty'];

                        //$client->sync_date = time();
                        $count_new++;

                    }else {
                        if(!empty($aUser['email']) && $client->email != $aUser['email']) {
                            $client->email = $aUser['email'];
                        }
                        if(!empty($aUser['fio']) && $client->name != $aUser['fio']) {
                            $client->name = $aUser['fio'];
                        }
                        if(!empty($aUser['phone']) && $client->mobile_phone != $aUser['phone']) {
                            $client->mobile_phone = $aUser['phone'];
                        }
                        $client->cashback = $aUser['cashback'];
                        $client->current_year_sended_places = $aUser['current_year_sended_places'];
                        $client->current_year_sended_prize_places = $aUser['current_year_sended_prize_places'];
                        $client->current_year_penalty = $aUser['current_year_penalty'];

                        $count_old++;
                    }
                    if(!$client->save(false)) {
                        throw new ForbiddenHttpException('Не удалось сохранить клиента');
                    }
                    $client->setField('sync_date', time());

                    $aIds[$aUser['id']] = $aUser['id'];
                }



                // пошлем обратно ответ на клиентский сервер со списком id записанных
                $request_2 = new \yii\httpclient\Client();
                $response = $request_2->createRequest()
                    ->setMethod('post')
                    ->setUrl(Yii::$app->params['clientServerUrl'].'user/set-sync-to-users?ids='.implode(',', $aIds))
                    ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
                    ->send();

                if ($response->statusCode == 200) {
                    echo "Клиенты созданы(".$count_new." шт)/обновлены(".$count_old." шт)\n";
                }else {
                    echo "Пришел ответ на запрос установки дат синхронизации со статусом ".$response->statusCode."\n";
                    echo "ссылка ".Yii::$app->params['clientServerUrl'].'user/set-sync-to-users?ids='.implode(',', $aIds)."\n";
                    exit;
                }

            }else {
                echo "нечего записывать \n";
            }

        }else {
            echo "Пришел ответ на запрос со статусом ".$response->statusCode."\n";
            exit;
        }

    }


    /*
     * Получение новых заявок и обновление данных по старым заявкам
     *
     * php yii client-server/get-not-sync-clientexts
     */
    /**
     * @throws ErrorException
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function actionGetNotSyncClientexts()
    {
        $request_1 = new \yii\httpclient\Client(); // это клиент запроса, а не Клиент-человек


        // curl -i -H "Authorization: SecretKey zLitjs_lUIthw908y" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-client.ru/serverapi/clientext/get-not-sync-clientexts
        $response = $request_1->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['clientServerUrl'].'clientext/get-not-sync-clientexts')
            //->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
            ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
            ->send();


        if ($response->statusCode == 200) {


            $clientserver_clientexts = $response->data;
            if(count($clientserver_clientexts) > 0) {

                $aIds = [];
                $aMainServerOrderId = [];

                foreach($clientserver_clientexts as $clientext) {
                    $aIds[$clientext['id']] = $clientext['id'];
                    $aMainServerOrderId[$clientext['main_server_order_id']] = $clientext['main_server_order_id'];
                }

                // обработка направлений
//                $aDirections = [];
//                foreach($clientserver_clientexts as $clientext) {
//                    $aIds[$clientext['id']] = $clientext['id'];
//                    $aMainServerOrderId[$clientext['main_server_order_id']] = $clientext['main_server_order_id'];
//                    $aDirections[$clientext['direction']] = $clientext['direction'];
//                }
                

//                $aDirectionsNameId = [];
//                foreach ($aDirections as $direction_name) {
//                    $cities_from_to = explode('-', $direction_name);
//                    $city_from = City::find()->where(['name' => $cities_from_to[0]])->one();
//                    if ($city_from == null) {
//                        self::sendMessageToAdmin('Город отравления не найден при идентификации направления direction_name='.$direction_name);
//                        throw new ForbiddenHttpException('Город отравления не найден при идентификации направления');
//                        //exit('Город отравления не найден при идентификации направления');
//                    }
//
//                    $city_to = City::find()->where(['name' => $cities_from_to[1]])->one();
//                    if ($city_to == null) {
//                        self::sendMessageToAdmin('Город прибытия не найден при идентификации направления direction_name='.$direction_name);
//                        throw new ForbiddenHttpException('Город прибытия не найден при идентификации направления');
//                        //exit('Город прибытия не найден при идентификации направления');
//                    }
//
//                    $direction = Direction::find()->where(['city_from' => $city_from->id, 'city_to' => $city_to->id])->one();
//                    if ($direction == null) {
//                        self::sendMessageToAdmin('Направление ' . $direction_name . ' не найдено. Необходимо создать данное направление.');
//                        throw new ForbiddenHttpException('Направление ' . $direction_name . ' не найдено. Необходимо создать данное направление.');
//                        //exit('Направление ' . $direction_name . ' не найдено. Необходимо создать данное направление.');
//                    }
//
//                    $aDirectionsNameId[$direction_name] = $direction->id;
//                }
                
                


                // делим все заявки на новые и старые (у старых есть заказы, которые нужно обновить)
                $new_clientexts = [];

                $orders_with_external_id = Order::find()->where(['external_id' => $aIds])->all();
                $orders_without_external_id = Order::find()->where(['id' => $aMainServerOrderId])->all();

                $aClientOrdersWithExternalId = ArrayHelper::index($orders_with_external_id, 'external_id');
                $aClientOrdersWithoutExternalId = ArrayHelper::index($orders_without_external_id, 'id');


                $oldClientextsWithExternalId = [];
                $oldClientextsWithoutExternalId = [];
                foreach($clientserver_clientexts as $clientext) {
                    if(isset($aClientOrdersWithExternalId[$clientext['id']])) {
                        $oldClientextsWithExternalId[$clientext['id']] = $clientext;
                    }elseif(isset($aClientOrdersWithoutExternalId[$clientext['main_server_order_id']])) {
                        $oldClientextsWithoutExternalId[$clientext['main_server_order_id']] = $clientext;
                    }else {
                        $new_clientexts[] = $clientext;
                    }
                }

                // создаем новые заказы из заявок
                if(count($new_clientexts) > 0) {

                    foreach($new_clientexts as $server_client_ext) {
                        $order = new Order();
                        $order = self::orderFillClientextData($order, $server_client_ext);

                        //echo "order:<pre>"; print_r($order); echo "</pre>";
                        if (!$order->save(false)) {

                            $msg = '';
                            foreach($server_client_ext as $param => $value) {
                                $msg .= "$param: $value<br />";
                            }

                            self::sendMessageToAdmin('Ошибка создания заказа из заявки id='.$server_client_ext['id'], $msg);
                            throw new ErrorException('Не сохранить заказ');
                        }

                        $order->setPay(false);

                        // если пришел сигнал об оплате, то отправляем на litebox сервер операцию "Приход" для фискализации
//                        if($order->paid_time > 0) {
//                            // $order->setPay(); - не стоит вызывать, так как paidSumm и другие поля заказа уже записаны
//                            LiteboxOperation::makeOperationSell($order);
//                        }

                        foreach($server_client_ext['passengers'] as $aPassenger) {
                            $passenger = Passenger::find()
                                ->where(['document_type' => $aPassenger['document_type']])
                                ->andWhere(['series_number' => $aPassenger['series_number']])
                                ->one();
                            if($passenger == null) {
                                $passenger = new Passenger();
                            }

                            $passenger->client_id = $order->client_id;
                            $passenger->series_number = $aPassenger['series_number'];
                            $passenger->fio = $aPassenger['fio'];
                            $passenger->date_of_birth = $aPassenger['date_of_birth'];
                            $passenger->citizenship = $aPassenger['citizenship'];
                            $passenger->gender = $aPassenger['gender'];
                            $passenger->document_type = $aPassenger['document_type'];

                            if(!$passenger->save(false)) {

                                $msg = '';
                                foreach($passenger as $param => $value) {
                                    $msg .= "$param: $value<br />";
                                }
                                self::sendMessageToAdmin('Не удалось сохранить пассажира id='.$server_client_ext['id'], $msg);
                                throw new ForbiddenHttpException('Не удалось сохранить пассажира');
                            }
                        }
                    }

                }





                // старым заказам меняем статус если заявка была отменена
                if(count($oldClientextsWithExternalId) > 0) {

                    //echo "aClientOrdersWithExternalId:<pre>"; print_r($aClientOrdersWithExternalId); echo "</pre>";

                    foreach($aClientOrdersWithExternalId as $external_id => $order) {
                        $client_ext = $oldClientextsWithExternalId[$external_id];
                        //if ($client_ext['status'] == 'canceled') {
                        if ($client_ext['status'] == 'canceled_by_client') {

                            $aFields = [
                                'status_setting_time' => $client_ext['status_setting_time'],
                                'cancellation_click_time' => $client_ext['cancellation_click_time'],
                                'cancellation_clicker_id' => $client_ext['cancellation_clicker_id'],
                                'canceled_by' => 'client'
                            ];

                            $order->setStatus('canceled', $aFields);
                            //$order->setField('canceled_by', 'client');


                            DispatcherAccounting::createLog('order_cancel', $order->id, time(), 0);// логируем Удаление заказа

                        }elseif($client_ext['status'] == 'canceled_by_operator') {

                            $aFields = [
                                'canceled_by' => 'operator'
                            ];
                            $order->setStatus('canceled', $aFields);
                            // $order->setField('canceled_by', 'operator');

                            DispatcherAccounting::createLog('order_cancel', $order->id, time(), 0);// логируем Удаление заказа

                        }elseif($client_ext['status'] == 'canceled_auto') {

                            $aFields = [
                                'canceled_by' => 'auto'
                            ];
                            $order->setStatus('canceled', $aFields);
                            //$order->setField('canceled_by', 'auto');

                            DispatcherAccounting::createLog('order_cancel', $order->id, time(), 0);// логируем Удаление заказа
                        }

//                        if($order->is_paid != $client_ext['is_paid']) {
//                            $order->setField('is_paid', $client_ext['is_paid']);
//                        }


                        if($client_ext['is_paid'] == true) { // раз оплачено и цена фиксированная, то
                            if($order->is_paid != true) {
                                $order->setField('is_paid', true);
                                // $order->setField('cash_received_time', time());// это время получения денег водителем - здесь не нужно!
                                $order->setField('paid_time', time()); // время оплаты (по факту время синхронизации после оплаты), погрешность +/- 30 секунд

                                // если пришел сигнал об оплате, то отправляем на litebox сервер операцию "Приход" для фискализации
                                LiteboxOperation::makeOperationSell($order);
                            }
                            if($order->use_fix_price != true) {
                                $order->setField('use_fix_price', true);
                            }
                            if($order->price != $client_ext['price']) {
                                $order->setField('price', $client_ext['price']);
                            }

                        }else {
                            // если например заказ был отменен, то оплаченный заказ перестанет быть оплаченным
                            if($order->use_fix_price == true) {
                                $order->setField('use_fix_price', false);
                            }
                            if($order->is_paid == true) {
                                $order->setField('is_paid', false);
                                $order->setField('paid_time', 0);
                            }
                        }

                        if($order->paid_summ != $client_ext['paid_summ']) {
                            $order->setField('paid_summ', $client_ext['paid_summ']);
                        }
                    }
                }

                if(count($oldClientextsWithoutExternalId) > 0) {
                    foreach($aClientOrdersWithoutExternalId as $order_id => $order) {
                        if(!isset($oldClientextsWithoutExternalId[$order_id])) {
                            continue;
                        }
                        $client_ext = $oldClientextsWithoutExternalId[$order_id];
                        if (in_array($client_ext['status'], ['canceled_by_client', 'canceled_by_operator', 'canceled_auto'])) {

                            $order->setField('external_id', $client_ext['id']);

                            if($client_ext['source_type'] == 'client_site') {
                                $order->setField('external_type', 'client_site');
                            }elseif($client_ext['source_type'] == 'main_site') {

                            }elseif($client_ext['source_type'] == 'application') {
                                $order->setField('external_type', 'application');
                            }

                            if($client_ext['status'] == 'canceled_by_client') {
                                $aFields = [
                                    'canceled_by' => 'client'
                                ];
                            }elseif($client_ext['status'] == 'canceled_by_operator') {
                                $aFields = [
                                    'canceled_by' => 'operator'
                                ];
                            }elseif($client_ext['status'] == 'canceled_auto') {
                                $aFields = [
                                    'canceled_by' => 'auto'
                                ];
                            }

                            $order->setStatus('canceled', $aFields);

//                            if($client_ext['status'] == 'canceled_by_client') {
//                                $order->setField('canceled_by', 'client');
//                            }elseif($client_ext['status'] == 'canceled_by_operator') {
//                                $order->setField('canceled_by', 'operator');
//                            }elseif($client_ext['status'] == 'canceled_auto') {
//                                $order->setField('canceled_by', 'auto');
//                            }

                            DispatcherAccounting::createLog('order_cancel', $order->id, time(), 0);// логируем Удаление заказа

                        }

                        if($order->paid_summ != $client_ext['paid_summ']) {
                            $order->paid_summ = $client_ext['paid_summ'];
                            $order->setField('paid_summ', $order->paid_summ);
                        }

//                        if($order->is_paid != $client_ext['is_paid']) {
//                            $order->setField('is_paid', $client_ext['is_paid']);
//                        }

                        $is_paid = false;
                        if($order->paid_summ >= $order->price) {
                            $is_paid = true;
                        }
                        if($is_paid == true) {
                            $order->setField('is_paid', true);
                            $order->setField('is_paid', true);
                            $order->setField('price', $order->source_price);
                        }else {
                            $order->setField('is_paid', false);
                        }
                    }
                }

                if(!Yii::$app->request->isConsoleRequest && (count($oldClientextsWithExternalId) > 0 || count($oldClientextsWithoutExternalId) > 0 || count($new_clientexts) > 0)) {
                    // если заявко-заказ только что создалась, то обновляем окно заявко-заказов
                    IncomingOrdersWidget::updateIncomingClientextOrders();
                }

//                // пошлем обратно ответ на клиентский сервер со списком id записанных заявок, чтобы там была установлена дата синхронизации
                // curl -i -H "Authorization: SecretKey zLitjs_lUIthw908y" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-client.ru/serverapi/clientext/set-sync-to-clientexts?ids=1,2,3,7



                $request_2 = new \yii\httpclient\Client();
                $response = $request_2->createRequest()
                    ->setMethod('post')
                    ->setUrl(Yii::$app->params['clientServerUrl'].'clientext/set-sync-to-clientexts?ids='.implode(',', $aIds))
                    //->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
                    ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
                    ->send();

                if ($response->statusCode == 200) {
                    echo "Заказы созданы(".count($new_clientexts)." шт)/обновлены(".(count($oldClientextsWithExternalId) + count($oldClientextsWithoutExternalId))." шт)\n";
                }else {
                    echo "Пришел ответ на запрос установки дат синхронизации со статусом ".$response->statusCode."\n";
                    // echo "url=".Yii::$app->params['clientServerUrl'].'clientext/set-sync-to-clientexts?ids='.implode(',', $aIds);
                    exit;
                }

            }else {
                echo "нечего записывать \n";
            }

        }else {
            echo "Пришел ответ на запрос получения клиентов со статусом ".$response->statusCode."\n";
            exit;
        }

    }



    /*
     * Получение новых запросов с клиентского сервера и обновление данных по старым запросам
     *
     * php yii client-server/get-not-sync-requests
     */
    /*
    public function actionGetNotSyncRequests()
    {
        $request_1 = new Client();

        $response = $request_1->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['clientServerUrl'].'request/get-not-sync-requests')
            ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
            ->send();

        if ($response->statusCode == 200) {

//            [3] => Array(
//                [id] => 5
//                [created_at] => 123456789
//                [direction] => АК
//                [date] => 01.01.2019
//                [phone] => +7-676-766-8686
//            )

            $clientserver_requests = $response->data;
            if(count($clientserver_requests) > 0) {

                // ищем уже существующие заказы связанные с полученными заявками
                $aIds = [];
                foreach($clientserver_requests as $request) {
                    $aIds[] = $request['id'];
                }

                $exist_orders = Order::find()->where(['external_id' => $aIds])->andWhere(['external_type' => 'client_server_request'])->all();

                $aExistOrders = [];
                $new_requests = [];
                $old_requests = [];
                if(count($exist_orders) > 0) {
                    $aExistOrders = ArrayHelper::map($exist_orders, 'external_id', 'id');
                }
                foreach($clientserver_requests as $request) {
                    if(!isset($aExistOrders[$request['id']])) {
                        $new_requests[] = $request;
                    }else {
                        $old_requests[] = $request;
                    }
                }


                if(count($new_requests) > 0) {
                    foreach($new_requests as $request) {
                        $order = new Order();
                        $order = self::orderFillRequestData($order, $request);

                        if(!$order->save(false)) {
                            echo "Не удалось создать заказ из запроса с клиенского сайта \n";
                            exit;
                        }
                    }
                }

//                if(count($old_requests) > 0) {
//                    foreach($old_requests as $request) {
//
//                    }
//                }


                // отправляем обновленнные данные о заявках в браузеры
                if(count($new_requests) > 0) {
                    IncomingOrdersWidget::updateIncomingRequestOrders();
                }

                // пошлем обратно ответ на клиентский сервер со списком id записанных заявок, чтобы там была установлена дата синхронизации
                // curl -i -H "Authorization: SecretKey zLitjs_lUIthw908y" "Accept:application/json" -H "Content-Type:application/json" -XPOST http://tobus-client.ru/serverapi/request/set-sync-to-requests?ids=1,2,3,7
                $request_2 = new Client();
                $response = $request_2->createRequest()
                    ->setMethod('post')
                    ->setUrl(Yii::$app->params['clientServerUrl'].'request/set-sync-to-requests?ids='.implode(',', $aIds))
                    ->setHeaders(['Authorization' => 'SecretKey '.ClientServer::$secretKey])
                    ->send();

                if ($response->statusCode == 200) {
                    echo "Заказы созданы(".count($new_requests)." шт)\n";
                }else {
                    echo "Пришел ответ на запрос установки дат синхронизации со статусом ".$response->statusCode."\n";
                    exit;
                }


            }else {
                echo "нечего записывать \n";
            }

        }else {
            echo "Пришел ответ на запрос получения клиентов со статусом ".$response->statusCode."\n";
            exit;
        }
    }
    */

    /*
    private static function orderFillRequestData($order, $request) {

//          Array(
//                [id] => 5
//                [created_at] => 123456789
//                [direction] => АК
//                [date] => 01.01.2019
//                [phone] => +7-676-766-8686
//            )

        //$order->is_mobile = 0;
        $order->external_id = $request['id'];
        $order->external_type = 'client_server_request';
        $order->external_created_at = $request['created_at'];
        $order->sync_date = time();

        $client = \app\models\Client::find()
            ->where(['mobile_phone' => $request['phone']])
            ->one();
        if($client == null) {
            $client = new \app\models\Client();
            $client->mobile_phone = $request['phone'];


            if(!$client->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить клиента');
            }
        }

        $order->client_id = $client->id;
        $order->client_name = $client->name;


        $order->status_id = 0;
        $order->status_setting_time = time();
        $order->date = $request['date'];// 10.07.2018
        $order->direction_id = ($request['direction'] == 'АК' ? 1 : 2);

        return $order;
    }*/


    /**
     * @param $order
     * @param $server_client_ext
     * @return mixed
     * @throws ErrorException
     */
    private static function orderFillClientextData($order, $server_client_ext) {

        //$order->is_mobile = $server_client_ext['is_mobile'];
        $order->external_id = $server_client_ext['id'];
        //$order->external_type = 'client_server_client_ext';
        if($server_client_ext['source_type'] == 'main_site') {
            $order->external_type = '';
        }elseif($server_client_ext['source_type'] == 'client_site') {
            $order->external_type = 'client_site';
        }elseif($server_client_ext['source_type'] == 'application') {
            $order->external_type = 'application';
        }

        $order->sync_date = time(); // если стоит дата синхронизации, то данные этой заявки не попадут / не обновят связанную заявку

        $client = null;
        if(!empty($server_client_ext['email'])) {
            $client = \app\models\Client::find()
                ->where(['email' => $server_client_ext['email']])
                ->one();
        }
        if($client == null) {
            $client = \app\models\Client::find()
                ->where(['mobile_phone' => $server_client_ext['phone']])
                ->one();
        }


        // создание клиента должно было произойти ранее при синхронизации клиентов-пользователей
        if($client == null) {
            // throw new ErrorException('Клиент не найден');

            // заказ без клиента, создадим нового
            $client = new Client();
            $client->name = $server_client_ext['fio'];
            $client->email = $server_client_ext['email'];
            $client->mobile_phone = $server_client_ext['phone'];
            if(!$client->save(false)) {
                throw new ForbiddenHttpException('Не удалось создать пользователя');
            }
        }

        $order->client_id = $client->id;
        $order->client_name = $client->name;

        $order->status_id = 0;
        $order->trip_id = $server_client_ext['trip_id'];
        // $order->status_setting_time = time();
        $order->status_setting_time = $server_client_ext['trip_id'];
        $order->cancellation_click_time = $server_client_ext['cancellation_click_time'];
        $order->cancellation_clicker_id = $server_client_ext['cancellation_clicker_id'];


        $order->canceled_by = '';
        $order->date = $server_client_ext['data'];// 10.07.2018
        //$order->direction_id = $aDirectionsNameId[$server_client_ext['direction']];
        $order->direction_id = $server_client_ext['direction_id'];

        //$order->yandex_point_from_id = $server_client_ext['yandex_point_from_id'];

        $yandex_point_from = YandexPoint::find()->where(['name' => $server_client_ext['yandex_point_from_name']])->one();
        if($yandex_point_from != null) {
            $order->yandex_point_from_id = $yandex_point_from->id;
        }
        $order->yandex_point_from_name = $server_client_ext['yandex_point_from_name'];
        $order->yandex_point_from_lat = $server_client_ext['yandex_point_from_lat'];
        $order->yandex_point_from_long = $server_client_ext['yandex_point_from_long'];


        //$order->yandex_point_to_id = $server_client_ext['yandex_point_to_id'];
        $yandex_point_to = YandexPoint::find()->where(['name' => $server_client_ext['yandex_point_to_name']])->one();
        if($yandex_point_to != null) {
            $order->yandex_point_to_id = $yandex_point_to->id;
        }
        $order->yandex_point_to_name = $server_client_ext['yandex_point_to_name'];
        $order->yandex_point_to_lat = $server_client_ext['yandex_point_to_lat'];
        $order->yandex_point_to_long = $server_client_ext['yandex_point_to_long'];

        $order->time_air_train_arrival = $server_client_ext['time_air_train_arrival'];
        
        $order->suitcase_count = $server_client_ext['suitcase_count'];
        $order->bag_count = $server_client_ext['bag_count'];
        // $order->fio = $server_client_ext['fio'];
        $order->time_confirm = $server_client_ext['time_confirm'];

//        $trip = Trip::find()
//            ->where(['date' => $server_client_ext['data']])
//            ->andWhere(['mid_time' => $server_client_ext['time']])
//            ->one();
//        if($trip != null) {
//            $order->trip_id = $trip->id;
//        }

        if($server_client_ext['places_count'] == 0) {
            $order->is_not_places = 1;
        }
        $order->places_count = $server_client_ext['places_count'];
        $order->student_count = $server_client_ext['student_count'];
        $order->child_count = $server_client_ext['child_count'];
        $order->is_not_places = $server_client_ext['is_not_places'];
        $order->prize_trip_count = $server_client_ext['prize_trip_count'];

        $order->source_price = $server_client_ext['price'];
        $order->paid_summ = $server_client_ext['paid_summ'];
        if($server_client_ext['is_paid'] == true) {
            $order->use_fix_price = true;
            $order->price = $server_client_ext['price']; // сразу устанавливаем цену, ибо она уже меняться не будет
            //$order->cash_received_time = time(); // это время получения денег водителем - здесь не нужно!
            $order->is_paid = true;
            $order->paid_time = time(); // время оплаты (по факту время синхронизации после оплаты), погрешность +/- 30 секунд
        }else {
            $order->use_fix_price = false;
            $order->is_paid = false;
            $order->paid_time = 0;
        }
        // $order->setPay(false);


        if($server_client_ext['source_type'] == 'application') {
            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
            if ($informer_office != null) {
                $order->informer_office_id = $informer_office->id;
            }
        }

        //$order->price = $server_client_ext['price'];
        //$order->use_fix_price = true;

        return $order;
    }


    public static function sendMessageToAdmin($theme, $msg = 'test') {

        Yii::$app->mailer->compose()
            ->setFrom('admin@developer.almobus.ru')
            ->setTo('vlad.shetinin@gmail.com')
            //->setTo('nara-dress@yandex.ru')
            ->setSubject($theme)
            //->setTextBody($msg)
            ->setHtmlBody($msg)
            ->send();
    }



}
