<?php

namespace app\controllers;

use app\models\Call;
use app\models\ClientServer;
use app\models\InformerOffice;
use app\models\Passenger;
use app\models\OrderPassenger;
use app\models\Setting;
use app\models\SocketDemon;
use app\models\User;
use app\models\UserRole;
use app\models\YandexPoint;
use app\widgets\IncomingOrdersWidget;
use Yii;
use app\models\Order;
use app\models\OrderCancellationReason;
use app\models\Client;
use app\models\OrderSearch;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Helper;
use yii\helpers\Url;
use app\models\Trip;
use app\models\OrderStatus;
use app\models\Schedule;
use app\models\Transport;
use app\models\TripTransport;
use app\models\Point;
use app\models\Street;
use app\models\Direction;
use app\models\DispatcherAccounting;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionAjaxGetForm()
    {
        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();

        $order_id = isset($post['order_id']) ? intval($post['order_id']) : 0;
        $date = isset($post['date']) ? $post['date'] : '';
        $trip_id = isset($post['trip_id']) ? intval($post['trip_id']) : 0;
        $direction_id = isset($post['direction_id']) ? intval($post['direction_id']) : '';

        $trip_transport_id = isset($post['trip_transport_id']) ? intval($post['trip_transport_id']) : 0;
        $trip_transport = null;
        if($trip_transport_id > 0) {
            $trip_transport = TripTransport::find()->where(['id' => $trip_transport_id])->one();
        }
        $create_new_order = true;


        if(!empty($order_id)) {

            $mode = isset($post['mode']) ? $post['mode'] : 'view'; // режим работы с полями телефонными
            $order = $this->findModel($order_id);

            $client = $order->client;
            if($client == null) {
                $client = new Client();
            }
            $date = date('d.m.Y', $order->date);
            $trip_id = $order->trip_id;

            // если заказ имеет статус 'canceled', значит создаю копию заказа
            $canceled_order_status = OrderStatus::getByCode('canceled');
            if($order->status_id == $canceled_order_status->id) {

                $new_order = new Order();
                //$new_order->is_mobile = $order->is_mobile;
                //$new_order->client_ext_id = $order->client_ext_id;
                // копия заказа не может быть аналогом заказа на клиентском сервере. Правильнее если клиенский сервер заново создаст заказ.
                //$new_order->external_id = $order->external_id;
                //$new_order->external_type = $order->external_type;
                $new_order->sync_date = $order->sync_date;
                $new_order->client_id = $order->client_id;
                $new_order->client_name = $order->client_name;
                //$new_order->status_id = $created_order_status->id;
                //$new_order->status_setting_time = time();
                $new_order->date = $order->date;
                $new_order->direction_id = $order->direction_id;
                $new_order->client_position_from_lat = $order->client_position_from_lat;
                $new_order->client_position_from_long = $order->client_position_from_long;
                $new_order->yandex_point_from_id = $order->yandex_point_from_id;
                $new_order->yandex_point_from_name = $order->yandex_point_from_name;
                $new_order->yandex_point_from_lat = $order->yandex_point_from_lat;
                $new_order->yandex_point_from_long = $order->yandex_point_from_long;
                $new_order->time_air_train_arrival = $order->time_air_train_arrival;
                $new_order->yandex_point_to_id = $order->yandex_point_to_id;
                $new_order->yandex_point_to_name = $order->yandex_point_to_name;
                $new_order->yandex_point_to_lat = $order->yandex_point_to_lat;
                $new_order->yandex_point_to_long = $order->yandex_point_to_long;
                $new_order->time_air_train_departure = $order->time_air_train_departure;
                $new_order->trip_id = $order->trip_id;
                $new_order->informer_office_id = $order->informer_office_id;
                $new_order->is_not_places = $order->is_not_places;
                $new_order->places_count = $order->places_count;
                $new_order->student_count = $order->student_count;
                $new_order->child_count = $order->child_count;
                $new_order->bag_count = $order->bag_count;
                $new_order->suitcase_count = $order->suitcase_count;
                $new_order->oversized_count = $order->oversized_count;
                $new_order->prize_trip_count = $order->prize_trip_count;
                $new_order->comment = (!empty($order->comment) ? $order->comment.' ВПУ id '.$order->id : 'ВПУ id '.$order->id);
                $new_order->additional_phone_1 = $order->additional_phone_1;
                $new_order->additional_phone_2 = $order->additional_phone_2;
                $new_order->additional_phone_3 = $order->additional_phone_3;
                $new_order->time_sat = $order->time_sat;
                $new_order->time_satter_user_id = $order->time_satter_user_id;
                $new_order->confirmed_time_sat = $order->confirmed_time_sat;
                $new_order->confirmed_time_satter_user_id = $order->confirmed_time_satter_user_id;
                $new_order->source_price = $order->source_price;
                $new_order->use_fix_price = $order->use_fix_price;
                $new_order->price = $order->price;
                $new_order->time_confirm = $order->time_confirm;
                $new_order->time_confirm_auto = $order->time_confirm_auto;
                $new_order->time_confirm_sort = $order->time_confirm_sort;
                $new_order->time_vpz = $order->time_vpz;
                $new_order->is_confirmed = $order->is_confirmed;
                $new_order->first_opened_form_time = $order->first_opened_form_time;
                $new_order->first_writedown_click_time = $order->first_writedown_click_time;
                $new_order->last_writedown_click_time = $order->last_writedown_click_time;
                $new_order->first_opened_form_user_id = $order->first_opened_form_user_id;
                $new_order->first_writedown_clicker_id = $order->first_writedown_clicker_id;
                $new_order->last_writedown_clicker_id = $order->last_writedown_clicker_id;
                $new_order->first_confirm_click_time = $order->first_confirm_click_time;
                $new_order->last_confirm_click_time = $order->last_confirm_click_time;
                $new_order->first_confirm_clicker_id = $order->first_confirm_clicker_id;
                $new_order->last_confirm_clicker_id = $order->last_confirm_clicker_id;
                $new_order->radio_confirm_now = $order->radio_confirm_now;
                $new_order->radio_group_1 = $order->radio_group_1;
                $new_order->radio_group_2 = $order->radio_group_2;
                $new_order->radio_group_3 = $order->radio_group_3;
                $new_order->confirm_selected_transport = $order->confirm_selected_transport;
                $new_order->fact_trip_transport_id = $order->fact_trip_transport_id;
                $new_order->fact_trip_transport_car_reg = $order->fact_trip_transport_car_reg;
                $new_order->has_penalty = $order->has_penalty;
                $new_order->penalty_comment = $order->penalty_comment;
                $new_order->penalty_time = $order->penalty_time;
                $new_order->penalty_author_id = $order->penalty_author_id;
                $new_order->relation_order_id = $order->relation_order_id;
                $new_order->is_test = $order->is_test;
                $new_order->created_at = time();

                $create_new_order = false;
                if(!$new_order->save(false)) {
                    throw new ForbiddenHttpException('Не удалось создать новый заказ из удаленного');
                }

                // подмена
                $order = $new_order;
            }

            // функция calculatePrice не записывает в заказ цену, отключаю ее вызов
            // $order->calculatePrice; // этот метод для заказов с "признаком формирования цены" прописывает цену в заказ(если признак указан соответствующий)

            if(empty($order->first_opened_form_time)) {
                $order->setField('first_opened_form_time', time());
                $order->setField('first_opened_form_user_id', Yii::$app->user->id);

                // если была открыта форма для редактирования заявко-заказа (то в окне заяво-заказов подсвечивается текущий заявко-заказ)
                IncomingOrdersWidget::updateIncomingClientextOrders();
            }

        }else { // заказ существует

            $mode = isset($post['mode']) ? $post['mode'] : 'edit'; // режим работы с полями телефонными
            $order = new Order();
            $client = new Client();

            $order->date = !empty($date) ? strtotime($date) : '';
            $order->temp_identifier = Yii::$app->security->generateRandomString();
            $order->first_opened_form_time = time();
            $order->first_opened_form_user_id = Yii::$app->user->id;

            if (!empty($trip_id)) {
                $trip = Trip::findOne($trip_id);
                if ($trip == null) {
                    throw new ForbiddenHttpException('Рейс ' . $trip_id . ' не найден');
                }

                $order->date = $trip->date;
                $order->direction_id = $trip->direction_id;
                $order->trip_id = $trip->id;

                if($trip_transport != null && $trip_transport->transport != null) {
                    $order->fact_trip_transport_id = $trip_transport->id;
                    $order->fact_trip_transport_car_reg = $trip_transport->transport->car_reg;
                }

                $date = date('d.m.Y', $order->date);
            }

            if(empty($order->direction_id) && !empty($direction_id)) {
                $order->direction_id = $direction_id;
            }


            // если в этот момент есть активный звонок, то данные клиента этого звонка подставляются в форму
            // активный звонок - для входящих это начало разговора, для исходящих - это звонок привязанный к текущему оператору
            $active_call = Call::find()
                ->where(['handling_call_operator_id' => Yii::$app->user->id])
                //->andWhere(['>', 't_answer', 0])
                ->andWhere([
                    'OR',
                    ['>', 't_hungup', time()],
                    ['ats_eok_time' => 0],
                    ['ats_eok_time' => NULL],
                ])->one();


            if($active_call != null && !empty($active_call->operand)) {

                $client = Client::getClientByMobilePhone($active_call->operand);
                if($client != null && !empty($order->direction_id)) {

                    list(
                        $last_order,
                        $yandexPointFrom,
                        $order->yandex_point_from_id,
                        $order->yandex_point_from_name,
                        $order->yandex_point_from_lat,
                        $order->yandex_point_from_long,

                        $yandexPointTo,
                        $order->yandex_point_to_id,
                        $order->yandex_point_to_name,
                        $order->yandex_point_to_lat,
                        $order->yandex_point_to_long

                        ) = $client->getLastOrderData($order->direction_id);
                }

                if($client == null) {
                    $client = new Client();
                    $client->mobile_phone = $active_call->operand;
                    $mode = 'edit';

                }else {
                    $mode = 'view';
                }
            }


            //echo "order:<pre>"; print_r($order); echo "</pre>";
        }
        $day_code = Helper::getDayCode($date);


        if(empty($order->informer_office_id)) {

            $informer_office = null;
            if(!empty($order->external_id) && $order->external_type == 'application') {
                // установи фикс цену = пришедшей цене
                $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
            }elseif(!empty($order->external_id) && $order->external_type == 'client_site') {
                // установи фикс цену = пришедшей цене
                $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
            }else {
                $informer_office = InformerOffice::find()->where(['code' => 'default'])->one();
            }
            if($informer_office != null) {
                $order->informer_office_id = $informer_office->id;
            }
        }


        // Если это направление КА, и точка "Куда" пустая, то ставлю значение по умолчанию "Куда" - "АВ - Автовокзал Альм." - yandex_point_id=13
        if($order->direction_id == 2 && empty($order->yandex_point_to_name)) {
            $yandex_point_to = YandexPoint::find()->where(['id' => 13])->one();
            if($yandex_point_to == null) {
                throw new ForbiddenHttpException('Яндекс-точка по умолчанию не найдена ("АВ - Автовокзал Альм.")');
            }

            $order->yandex_point_to_id = $yandex_point_to->id;
            $order->yandex_point_to_name = $yandex_point_to->name;
            $order->yandex_point_to_lat = $yandex_point_to->lat;
            $order->yandex_point_to_long = $yandex_point_to->long;
        }

        // сохраняем заказ в базе если он еще не существует, и дальше работает только с фактическим заказом а не призраком
        if(empty($order->id) && $create_new_order == true) {
            $order->scenario = 'create_empty';
            $order->save();
        }

        return [
            'success' => true,
            'day_code' => $day_code,
            'html' => $this->renderAjax('form.php', [
                'order' => $order,
                'client' => $client,
                'mode' => $mode,
                //'clear_phones' => $clear_phones,
                //'active_call' => $active_call
            ]),
            'phones_block' => $this->renderAjax('phones-block.php', [
                'order' => $order,
                'client' => $client,
                'mode' => $mode,
            ]),
            'order_id' => $order_id,
            'date' => $date,
            'trip_id' => $trip_id,
            'class' => Helper::getClassByDayCode($day_code),
            'title' => Order::getOrderCreateTitle($day_code, $order_id > 0, $trip_id),
        ];
    }


    public function actionAjaxGetPassengersForm()
    {
        Yii::$app->response->format = 'json';


        $order_id = intval(Yii::$app->request->post('order_id'));
        $order = $this->findModel($order_id);
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        $places_count = Yii::$app->request->post('places_count');
        if($places_count != null) {
            $order->places_count = intval($places_count);
        }

        $client_id = intval(Yii::$app->request->post('client_id'));
        if($client_id > 0) {
            $order->client_id = $client_id;
        }

        return [
            'success' => true,
            'html' => $this->renderAjax('passengers-form.php', [
                'order' => $order,
            ]),
        ];
    }


    public function actionAjaxSavePassenger() {

        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();

        //$order_passenger_id = isset($post['OrderPassenger']['id']) ? intval($post['OrderPassenger']['id']) : 0;
        $order_id = isset($post['OrderPassenger']['order_id']) ? intval($post['OrderPassenger']['order_id']) : 0;
        $passenger_id = isset($post['OrderPassenger']['passenger_id']) ? intval($post['OrderPassenger']['passenger_id']) : 0;
        if($order_id > 0 && $passenger_id > 0) {

            $order_passenger = OrderPassenger::find()
                ->where(['order_id' => $order_id])
                ->andWhere(['passenger_id' => $passenger_id])
                ->one();
            if($order_passenger == null) {
                $order_passenger = new OrderPassenger();
            }

            $passenger = Passenger::find()->where(['id' => $passenger_id])->one();


        }else {
            $order_passenger = new OrderPassenger();
            $passenger = new Passenger();
        }


        if(
            $passenger->load($post)
            && $passenger->save()
            && $order_passenger->load($post)
            && ($order_passenger->passenger_id = $passenger->id)
            && $order_passenger->save()
        ) {

            return [
                'success' => true,
                'passenger_id' => $passenger->id,
                'order_passenger_id' => $order_passenger->id,
            ];

        }else {

            return [
                'success' => false,
                'passenger_errors' => $passenger->validate() ? '' : $passenger->getErrors(),
                'order_passenger_errors' => $order_passenger->validate() ? '' : $order_passenger->getErrors(),
            ];
        }
    }


    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Заказ не найден');
        }
    }

    /*
     * Это событие не используется, но пока не удаляю
     */
    public function actionAjaxCreateOrder()
    {
        Yii::$app->response->format = 'json';

        $order = new Order();
        $post = Yii::$app->request->post();

        $mode = isset($post['mode']) ? $post['mode'] : 'edit'; // режим работы с полями телефонными
        //$clear_phones = isset($post['clear_phones']) ? $post['clear_phones'] : false;

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null) {
            $client = new Client();
        }

        $order->scenario = 'writedown_button_create';


        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            if(!$client->save(false)) {
                throw new ErrorException('Не удалось сохранить клиента');
            }

            $order->client_id = $client->id;
//            if(!empty($order->client_ext_id)) {
//                $client_ext = $order->clientExt;
//                $order->client_server_ext_id = $client_ext->client_server_ext_id;
//                $order->price = $client_ext->price;
//            }


            $order->first_writedown_click_time = time();
            $order->first_writedown_clicker_id = Yii::$app->user->id;


            if(!$order->save()) {
                throw new ErrorException('Не удалось сохранить заказ');
            }else {

                if(!empty($order->first_confirm_click_time)) {
                    DispatcherAccounting::createLog('order_confirm', $order->id, $order->first_confirm_click_time);// логируем Подтверждение заказа
                }

                DispatcherAccounting::createLog('order_create', $order->id);// логируем Первичную запись
            }

            //$day_code = Helper::getDayCode(date('d.m.Y', $order->date));

            $reverse_form_html = '';
            $reverse_phones_block = '';
            $reverse_order_temp_identifier = '';
            if($order->radio_group_3 == 1) { // открываем тогда форму на обратное направление

                $revert_direction = $order->direction->revertDirection;
                if($revert_direction != null) {

                    $reverse_order = new Order();
                    $reverse_order->date = $order->date;
                    $reverse_order->direction_id = $revert_direction->id;

                    $reverse_order->is_not_places = $order->is_not_places;
                    $reverse_order->places_count = $order->places_count;
                    $reverse_order->student_count = $order->student_count;
                    $reverse_order->child_count = $order->child_count;
                    $reverse_order->bag_count = $order->bag_count;
                    $reverse_order->suitcase_count = $order->suitcase_count;
                    $reverse_order->oversized_count = $order->oversized_count;
                    $reverse_order->use_fix_price = $order->use_fix_price;
                    $reverse_order->price = $order->price;
                    $reverse_order->comment = $order->comment;
                    $reverse_order->additional_phone_1 = $order->additional_phone_1;
                    $reverse_order->additional_phone_2 = $order->additional_phone_2;
                    $reverse_order->additional_phone_3 = $order->additional_phone_3;

                    // точки Откуда/Куда должны формироваться на основе данных последнего заказа клиента по этому направлению
                    list(
                        $last_order,
                        $yandexPointFrom,
                        $reverse_order->yandex_point_from_id,
                        $reverse_order->yandex_point_from_name,
                        $reverse_order->yandex_point_from_lat,
                        $reverse_order->yandex_point_from_long,

                        $yandexPointTo,
                        $reverse_order->yandex_point_to_id,
                        $reverse_order->yandex_point_to_name,
                        $reverse_order->yandex_point_to_lat,
                        $reverse_order->yandex_point_to_long

                        ) = $client->getLastOrderData($reverse_order->direction_id);


                    if(empty($reverse_order->informer_office_id)) {

                        if(!empty($reverse_order->external_id) && $reverse_order->external_type == 'application') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }elseif(!empty($reverse_order->external_id) && $reverse_order->external_type == 'client_site') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }else {
                            $informer_office = InformerOffice::find()->where(['code' => 'default'])->one();
                        }
                        if($informer_office != null) {
                            $reverse_order->informer_office_id = $informer_office->id;
                        }
                    }

                    $reverse_form_html = $this->renderAjax('form.php', [
                        //'day_code' => $day_code,
                        'order' => $reverse_order,
                        'client' => $client,
                        'mode' => 'edit',
                        //'clear_phones' => $clear_phones
                    ]);
                    $reverse_order_temp_identifier = Yii::$app->security->generateRandomString();


                    $reverse_phones_block = $this->renderAjax('phones-block.php', [
                        'order' => $order,
                        'client' => $client,
                        'mode' => $mode,
                    ]);

                }

            }elseif($order->radio_group_3 == 3) {// открываем связанную форму "+1 к заказу"

                $revert_direction = $order->direction->revertDirection;
                if($revert_direction != null) {

                    $client = new Client();

                    $relation_order = new Order();
                    $relation_order->date = $order->date;
                    $relation_order->direction_id = $order->direction_id;
                    $relation_order->trip_id = $order->trip_id;

                    $relation_order->relation_order_id = $order->id;

                    if(empty($relation_order->informer_office_id)) {

                        if(!empty($relation_order->external_id) && $relation_order->external_type == 'application') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }elseif(!empty($relation_order->external_id) && $relation_order->external_type == 'client_site') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }else {
                            $informer_office = InformerOffice::find()->where(['code' => 'default'])->one();
                        }
                        if($informer_office != null) {
                            $relation_order->informer_office_id = $informer_office->id;
                        }
                    }

                    $reverse_order_temp_identifier = Yii::$app->security->generateRandomString();
                    $reverse_form_html = $this->renderAjax('form.php', [
                        //'day_code' => $day_code,
                        'order' => $relation_order,
                        'client' => $client,
                        'mode' => 'edit',
                        //'clear_phones' => $clear_phones
                    ]);

                    $reverse_phones_block = $this->renderAjax('phones-block.php', [
                        'order' => $order,
                        'client' => $client,
                        'mode' => $mode,
                    ]);
                }
            }

            return [
                'success' => true,
                'reverse_form_html' => $reverse_form_html,
                'reverse_order_temp_identifier' => $reverse_order_temp_identifier,
                'reverse_phones_block' => $reverse_phones_block
            ];
        }else {

            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
                'client_errors' => $client->validate() ? '' : $client->getErrors(),
            ];
        }
    }


    /**
     * @param $id
     * @return array
     * @throws ErrorException
     * @throws ForbiddenHttpException
     * @throws \yii\base\Exception
     */
    public function actionAjaxUpdateOrder($id)
    {
        Yii::$app->response->format = 'json';


        $order = Order::findOne($id);
        if($order == null) {
            throw new ErrorException('Заказ не найден');
        }

        $post = Yii::$app->request->post();

        $mode = isset($post['mode']) ? $post['mode'] : 'view'; // режим работы с полями телефонными
        // $clear_phones = isset($post['clear_phones']) ? $post['clear_phones'] : false;

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null) {
            $client = new Client();
        }

        $order->scenario = 'writedown_button_update';
        //$order->last_writedown_click_time = time();
        //$order->last_writedown_clicker_id = Yii::$app->user->id; //User::getCookieId();

        $was_canceled = false;
        if($order->status != null && $order->status->code == 'canceled') {
            $was_canceled = true;
            $new_order_status = OrderStatus::getByCode('created');
            $order->status_id = $new_order_status->id;
        }

//        echo "status_code=".$order->status->code." \n";
//        exit('sad');

        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            if(!$client->save(false)) {
                throw new ErrorException('Не удалось сохранить клиента');
            }

            if($order->trip == null) {
                throw new ForbiddenHttpException('Не существует рейса '.$order->trip_id.'. Снова выберите Дату и данные по рейсам обновяться.');
            }

            $order->client_id = $client->id;

            if(!$order->save(false)) {
                throw new ErrorException('Не удалось сохранить заказ');
            }else {

                // отмена отмененного заказа - надо делать через метод!!!
                //$order_status = $order->status;
                //if($order_status != null && $order_status->code == 'canceled') {
                if($was_canceled == true) {
                    //echo "status_code=".$order->status->code." \n";

                    $order->setStatus('created');

                    if($order->client_id > 0) {
                        //Client::recountSendedCanceledReliabilityCounts($order->client_id, 0, 0, -1, -$order->places_count);
                        $client = $order->client;
                        //Client::recountSendedCanceledReliabilityCounts($order->client_id, 0, 0, -1, -$order->places_count);
                        if($client != null) {
                            $client->recountSendedCanceledReliabilityCounts($order, 0, 0, -1, -$order->places_count);
                        }
                    }
                    /**/
                }

                if(empty($order->first_writedown_click_time)) {

                    $order->setField('first_writedown_click_time', time());
                    $order->setField('first_writedown_clicker_id', Yii::$app->user->id);
                    DispatcherAccounting::createLog('order_create', $order->id);// логируем Первичную запись

                }else {

                    $order->setField('last_writedown_click_time', time());
                    $order->setField('last_writedown_clicker_id', Yii::$app->user->id);
                    DispatcherAccounting::createLog('order_update', $order->id);// логируем Редактирование заказа
                }
            }



            // $day_code = Helper::getDayCode(date('d.m.Y', $order->date));

            $reverse_form_html = '';
            $reverse_order_temp_identifier = '';
            $reverse_phones_block = '';

            // отправка пуша пользователю
            if($order->external_type == 'application' && $client != null && !empty($client->mobile_phone)) {

                $text = '';
                if ($order->radio_group_1 > 0) {// выбрано "Подтвердить сейчас"
                    $list = $order->getRadioGroup1();
                    if ($order->radio_group_1 == 1) { //  АК: подъедет машина , будьте собраны и готовы в 04 : 00, без звонка не выходите
                        $text = $list[1];
                    } elseif ($order->radio_group_1 == 2) { // КА: вам нужно быть на «12 школа, Джалиля» в 04 : 00, подъедет
                        $text = $list[2];
                    }

                    ClientServer::sendPush($client->mobile_phone, 'Диспетчерская Альмобус', $text, 'with_sync_clientext', $order->external_id);
                    //$order->push_send_time = time();
                    $order->setField('push_send_time', time());
                    $order->setField('push_confirm_time', NULL);
                    $order->setField('push_rejection_time', NULL);
                }
                elseif ($order->radio_group_2 > 0) {// выбрано "Не подтверждать"

                    $list = $order->getRadioGroup2();
                    if ($order->radio_group_2 == 1) { // Мы позвоним вам сегодня до 10:00 и скажем точное время и машину
                        $text = $list[1];
                    } elseif ($order->radio_group_2 == 2) { // Вчера вечером мы вам позвоним и скажем точное время и машину
                        $text = $list[2];
                    }

                    ClientServer::sendPush($client->mobile_phone, 'Диспетчерская Альмобус', $text, 'with_sync_clientext', $order->external_id);
                    //$order->push_send_time = time();
                    $order->setField('push_send_time', time());
                    $order->setField('push_confirm_time', NULL);
                    $order->setField('push_rejection_time', NULL);
                }

            }




            if($order->radio_group_3 == 1) { // открываем тогда форму на обратное направление (Когда поедете обратно, давайте вас запишем)

                $revert_direction = $order->direction->revertDirection;
                if($revert_direction != null) {

                    $reverse_order = new Order();
                    $reverse_order->date = $order->date;
                    $reverse_order->direction_id = $revert_direction->id;

                    $reverse_order->is_not_places = $order->is_not_places;
                    $reverse_order->places_count = $order->places_count;
                    $reverse_order->student_count = $order->student_count;
                    $reverse_order->child_count = $order->child_count;
                    $reverse_order->bag_count = $order->bag_count;
                    $reverse_order->suitcase_count = $order->suitcase_count;
                    $reverse_order->oversized_count = $order->oversized_count;
                    $reverse_order->use_fix_price = $order->use_fix_price;
                    $reverse_order->price = $order->price;
                    $reverse_order->comment = $order->comment;
                    $reverse_order->additional_phone_1 = $order->additional_phone_1;
                    $reverse_order->additional_phone_2 = $order->additional_phone_2;
                    $reverse_order->additional_phone_3 = $order->additional_phone_3;


                    // точки Откуда/Куда должны формироваться на основе данных последнего заказа клиента по этому направлению
                    list(
                        $last_order,
                        $yandexPointFrom,
                        $reverse_order->yandex_point_from_id,
                        $reverse_order->yandex_point_from_name,
                        $reverse_order->yandex_point_from_lat,
                        $reverse_order->yandex_point_from_long,

                        $yandexPointTo,
                        $reverse_order->yandex_point_to_id,
                        $reverse_order->yandex_point_to_name,
                        $reverse_order->yandex_point_to_lat,
                        $reverse_order->yandex_point_to_long

                        ) = $client->getLastOrderData($reverse_order->direction_id);


                    if(empty($reverse_order->informer_office_id)) {

                        if(!empty($reverse_order->external_id) && $reverse_order->external_type == 'application') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }elseif(!empty($reverse_order->external_id) && $reverse_order->external_type == 'client_site') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }else {
                            $informer_office = InformerOffice::find()->where(['code' => 'default'])->one();
                        }
                        if($informer_office != null) {
                            $reverse_order->informer_office_id = $informer_office->id;
                        }
                    }



                    $reverse_order_temp_identifier = Yii::$app->security->generateRandomString();
                    $reverse_form_html = $this->renderAjax('form.php', [
                        // 'day_code' => $day_code,
                        'order' => $reverse_order,
                        'client' => $client,
                        'mode' => 'edit',
                        // 'clear_phones' => $clear_phones
                    ]);

                    $reverse_phones_block = $this->renderAjax('phones-block.php', [
                        'order' => $order,
                        'client' => $client,
                        'mode' => $mode,
                    ]);
                }

            }elseif($order->radio_group_3 == 3) {// открываем связанную форму на обратное направление

                $revert_direction = $order->direction->revertDirection;
                if($revert_direction != null) {

                    $client = new Client();

                    $relation_order = new Order();
                    $relation_order->date = $order->date;
                    $relation_order->direction_id = $order->direction_id;
                    $relation_order->trip_id = $order->trip_id;

                    $relation_order->relation_order_id = $order->id;

                    if(empty($relation_order->informer_office_id)) {

                        if(!empty($relation_order->external_id) && $relation_order->external_type == 'application') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }elseif(!empty($relation_order->external_id) && $relation_order->external_type == 'client_site') {
                            // установи фикс.цену = пришедшей цене
                            $informer_office = InformerOffice::find()->where(['code' => 'mobile_app'])->one();
                        }else {
                            $informer_office = InformerOffice::find()->where(['code' => 'default'])->one();
                        }
                        if($informer_office != null) {
                            $relation_order->informer_office_id = $informer_office->id;
                        }
                    }

                    $reverse_order_temp_identifier = Yii::$app->security->generateRandomString();
                    $reverse_form_html = $this->renderAjax('form.php', [
                        // 'day_code' => $day_code,
                        'order' => $relation_order,
                        'client' => $client,
                        'mode' => 'edit',
                        // 'clear_phones' => $clear_phones
                    ]);

                    $reverse_phones_block = $this->renderAjax('phones-block.php', [
                        'order' => $order,
                        'client' => $client,
                        'mode' => $mode,
                    ]);
                }
            }

            return [
                'success' => true,
                'order' => $order,
                'client' => $client,
                'reverse_form_html' => $reverse_form_html,
                'reverse_order_temp_identifier' => $reverse_order_temp_identifier,
                'reverse_phones_block' => $reverse_phones_block
            ];

        }else {

            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
                'client_errors' => $client->validate() ? '' : $client->getErrors(),
            ];
        }
    }


    public function actionAjaxCheckTimeGettingIntoCar($order_id = 0)
    {
        Yii::$app->response->format = 'json';

        if($order_id > 0) {
            $order = Order::findOne($order_id);
            if($order == null) {
                throw new ForbiddenHttpException('Заказ не найден');
            }
        }else {
            $order = new Order();
        }

//        $order = Order::findOne($order_id);
//        if($order == null) {
//            throw new ForbiddenHttpException('Заказ не найден');
//        }

        $post = Yii::$app->request->post();

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null) {
            $client = new Client();
        }

        $order_status_id = $order->status_id;
        $order->scenario = 'writedown_button_update';



        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            if(!$client->save()) {
                throw new ErrorException('Не удалось сохранить клиента');
            }

            $order->client_id = $client->id;

            if($order->id > 0) {
                if (!$order->save()) {
                    throw new ErrorException('Не удалось сохранить заказ');
                } else {

                    // при нажатии "подтвердить" в форме заказа заказ с нулевым статусом получает 1-й статус при сохранении
                    //  - исправим эту ситуацию
                    $order->setField('status_id', $order_status_id);

                    DispatcherAccounting::createLog('order_confirm', $order->id, $order->first_confirm_click_time);// логируем Подтверждение заказа
                }


                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'confirm_click_time' => time(),
                    'confirm_clicker_id' => Yii::$app->user->id,
                ];

            } else { // заказ еще не создан
                return [
                    'success' => true,
                    'order_id' => 0,
                    'confirm_click_time' => time(),
                    'confirm_clicker_id' => Yii::$app->user->id //User::getCookieId()
                ];
            }
        }else {
            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
            ];
        }
    }


    public function actionAjaxCheckFormFields()
    {
        Yii::$app->response->format = 'json';
        $post = Yii::$app->request->post();

        //echo "post:<pre>"; print_r($post); echo "</pre>";

        $order = new Order();
        $order->scenario = 'check_form_fields';

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null && isset($post['Client']['email'])) {
            $client = Client::find()->where(['email' => $post['Client']['email']])->one();
        }
        if($client == null) {
            $client = new Client();
        }
        //echo "client:<pre>"; print_r($client); echo "</pre>";

        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            $time_confirm = $order->getYandexPointTimeConfirm();

            return [
                'success' => true,
                'time_confirm_auto' => $time_confirm,
                'time_confirm_auto_min_sec' => ($time_confirm > 0 ? date("H:i", $time_confirm) : '')
            ];
        }else {

            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
                'client_errors' => $client->validate() ? '' : $client->getErrors(),
            ];
        }
    }


    public function actionAjaxGetInformerOfficeDoTariff($informer_office_id) {

        Yii::$app->response->format = 'json';

        $informer_office = InformerOffice::find()->where(['id' => $informer_office_id])->one();
        if($informer_office == null) {
            throw new ForbiddenHttpException('Источник не найден');
        }

        $do_tariff = $informer_office->doTariff;

        return [
            'success' => true,
            'do_tariff' => $do_tariff
        ];
    }

    public function actionAjaxGetCalculatePrice()
    {
        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();

        if(isset($post['Order']['id']) && $post['Order']['id'] > 0) {
            $model = Order::find()->where(['id' => $post['Order']['id']])->one();
            if($model == null) {
                throw new ForbiddenHttpException('Заказ не найден');
            }
        }else {
            $model = new Order();
        }
        $model->scenario = 'calculate_price';

//        $use_fix_price = $post['Order']['use_fix_price'] == 'true' ? true : false;
//        $fix_price = floatval($post['Order']['fix_price']);

//        echo "use_fix_price=$use_fix_price fix_price=$fix_price";
//        exit;

        if ($model->load($post) && $model->validate()) {

            //echo "model:<pre>"; print_r($model); echo "</pre>";

//            if($model->use_fix_price == 'true') {
//                $model->use_fix_price = true;
//            }elseif($model->use_fix_price == 'false') {
//                $model->use_fix_price = false;
//            }

            //echo 'model_use_fix_price='.$model->use_fix_price.' modal_price='.$model->price."<br />";

//            if($use_fix_price == true) {
//                $model->use_fix_price = true;
//                $model->price = $fix_price;
//            }elseif($use_fix_price == 'false') {
//                $model->use_fix_price = false;
//            }

            //echo 'use_fix_price='.$model->use_fix_price.' price='.$model->price."<br />";


            $client = null;
            if(empty($model->client_id)) {
                if(isset($post['Client']['mobile_phone'])) {
                    $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
                }
                if($client != null) {
                    $model->client_id = $client->id;
                }
            }

//            $do_tariff = null;
//            $use_fix_price = $model->use_fix_price;
//            $informer_office = $model->informerOffice;
//            if($informer_office != null) {
//                $do_tariff = $informer_office->doTariff;
//                if($do_tariff != null) {
//                    $use_fix_price = $do_tariff->use_fix_price;
//                }
//            }
            $use_fix_price = $model->use_fix_price;

            $do_tariff = null;
            if($model->client_id > 0) {
                $do_tariff = $model->client->doTariff;
            }
            if($do_tariff == null) {
                $informer_office = $model->informerOffice;
                if ($informer_office != null) {
                    $do_tariff = $informer_office->doTariff;
                }
            }

            if($do_tariff != null) {
                $use_fix_price = $do_tariff->use_fix_price;
            }


            if($do_tariff != null && !empty($do_tariff->order_comment)) {
                $comment = $model->comment.' '.$do_tariff->order_comment;
            }else {
                $comment = $model->comment;
            }

            return [
                'success' => true,
                'price' => $model->calculatePrice, // а если пришли пустые значения, то как сработает расчет?
                'prizeTripCount' => $model->prizeTripCount, // кол-во призовых поездок
                'use_fix_price' => $use_fix_price,
                'comment' => $comment
            ];
        } else {
            return [
                'success' => false,
                'errors' => $model->getErrors()
            ];
        }
    }


    public function actionCancelOrderForm($id)
    {
        $model = $this->findModel($id);

        $model->scenario = 'cancel';
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->setStatus('canceled')) {

            DispatcherAccounting::createLog('order_cancel', $model->id);// логируем Удаление заказа

            Yii::$app->response->format = 'json';
            return [
                'success' => true,
            ];

        }else {
            return $this->renderAjax(
                'cancel-form.php', [
                    'order' => $model,
                ]
            );
        }
    }


    public function actionEditableOrder($id)
    {
        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);

        if (isset($_POST['hasEditable']))
        {
            if(isset($_POST['fact_trip_transport_id'])) {

                if(intval($_POST['fact_trip_transport_id']) > 0) { // устанавливаем значение

                    $trip_transport = TripTransport::findOne($_POST['fact_trip_transport_id']);
                    if ($trip_transport == null) {
                        throw new ForbiddenHttpException('trip_transport не найден');
                    }

                    $order->setField('confirm_selected_transport', false);
                    $order->setField('fact_trip_transport_id', intval($_POST['fact_trip_transport_id']));
                    $order->setField('time_sat', '');
                    $order->setField('confirmed_time_satter_user_id', '');
                    $order->setField('updated_at', time());

                    return ['output' => $trip_transport->transport->name4, 'message' => ''];

                }else { // очищаем значение
                    $order->setField('confirm_selected_transport', false);
                    $order->setField('fact_trip_transport_id', '');
                    $order->setField('time_sat', '');
                    $order->setField('confirmed_time_satter_user_id', '');
                    $order->setField('updated_at', time());
                    return ['output' => '', 'message' => ''];
                }

            }elseif(isset($_POST['time_confirm'])) {

                if(empty($_POST['time_confirm'])) {
                    $time_confirm = null;

                    $order->setField('time_confirm', '');
                    $order->setField('time_confirm_sort', null);
                    $order->setField('is_confirmed', false);
//                    $order->setField('plan_trip_transport_id', '');
                    $order->setField('confirm_selected_transport', false);
                    $order->setField('fact_trip_transport_id', '');
                    $order->setField('time_sat', '');
                    $order->setField('updated_at', time());

                    return ['output' => date('H:i', $time_confirm), 'message'=>''];

                }else {

                    $hours_minutes = explode(':', $_POST['time_confirm']);

                    $time_confirm = $order->date + 3600 * intval($hours_minutes[0]) + 60 * intval($hours_minutes[1]);
                    if($time_confirm == $order->time_confirm) {
                        return ['output' => date('H:i', $time_confirm), 'message' => ''];
                    }

                    //$order->scenario = 'confirm_button_click';
                    $order->scenario = 'writedown_button_update';
                    $order->time_confirm = $time_confirm;
                    $order->time_confirm_sort = 1000*intval($hours_minutes[0]) + 10*intval($hours_minutes[1]);

                    if($order->validate()) {
                        $order->setField('time_confirm', $order->time_confirm);
                        $order->setField('time_confirm_sort', $order->time_confirm_sort);
                        $order->setField('is_confirmed', false);
                        $order->setField('confirm_selected_transport', false);
                        $order->setField('updated_at', time());

                        DispatcherAccounting::createLog('order_confirm', $order->id, time());// логируем Подтверждение заказа

                        return ['output' => date('H:i', $time_confirm), 'message' => ''];

                    }else {
                        $aErrors = $order->getErrors();
                        $aListErrors = [];
                        foreach($aErrors as $field => $aFieldErrors) {
                            $aListErrors[] = implode('. ', $aFieldErrors)."\n";
                        }
                        throw new ForbiddenHttpException(implode('. ', $aListErrors));
                    }
                }

            }else {
                return ['output' => '', 'message'=>'Неизвестное поле'];
            }
        }else {
            throw new ForbiddenHttpException('Формат запроса не верен');
        }
    }


    public function actionAjaxUpdateOrders($trip_id) {

        Yii::$app->response->format = 'json';

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        $post = Yii::$app->request->post();
        //echo "post:<pre>"; print_r($post); echo "</pre>";

        $orders = Order::findAll($post['orders_id']);
        if(count($orders) == 0) {
            throw new ForbiddenHttpException('Нет заказов');
        }

        foreach($orders as $order) {
            if($order->trip_id != $trip->id) {
                throw new ForbiddenHttpException('Один из заказов не принадлежит текущему рейсу');
            }
        }
        // echo "orders:<pre>"; print_r($orders); echo "</pre>";

        if(isset($post['fact_trip_transport_id'])) {

            foreach($orders as $order) {

                if($order->fact_trip_transport_id != intval($_POST['fact_trip_transport_id'])) {
                    $order->setField('confirm_selected_transport', false);
                    $order->setField('fact_trip_transport_id', intval($_POST['fact_trip_transport_id'])); // изменение планового-ам порождает автоматическое изменение факт-ам
                    $order->setField('time_sat', '');

                }
            }

        }elseif(isset($post['time_confirm'])) {

            $time_confirm = intval($_POST['time_confirm']);

            if(!empty($time_confirm))
            {
                $hours_minutes = explode(':', $_POST['time_confirm']);
                $time_confirm = $trip->date + 3600 * intval($hours_minutes[0]) + 60 * intval($hours_minutes[1]);

                $keys = array_keys($orders);
                $order = $orders[$keys[0]];
                // echo "order:<pre>"; print_r($order); echo "</pre>"; exit;


                //$order->scenario = 'confirm_button_click';
                $order->scenario = 'writedown_button_update';
                $order->time_confirm = $time_confirm;
                $order->time_confirm_sort = 1000*intval($hours_minutes[0]) + 10*intval($hours_minutes[1]);
                if ($order->validate()) {
                    // массово присваиваем заказам значение
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_confirm', $order->time_confirm);
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_confirm_sort', $order->time_confirm_sort);
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'is_confirmed', '');
                    Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'confirm_selected_transport', false);

                } else {
                    // показываем ошибку одного из заказов

                    $aErrors = [];
                    foreach($order->getErrors() as $field => $aFieldErrors) {
                        if(count($aFieldErrors) > 0) {
                            $aErrors = array_merge($aFieldErrors, $aErrors);
                        }
                    }
                    throw new ForbiddenHttpException(implode('. ', $aErrors));
                    //throw new ForbiddenHttpException('Ошибка изменения времени подтверждения');
                    //throw new ForbiddenHttpException(implode('. ', $order->getErrors('time_confirm')));
                }
            }else {
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_confirm', '');
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_confirm_sort', null);
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'is_confirmed', '');
//                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'plan_trip_transport_id', '');
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'confirm_selected_transport', false);
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'fact_trip_transport_id', '');
                Order::setFields(ArrayHelper::map($orders, 'id', 'id'), 'time_sat', '');
            }

        }else {
            throw new ForbiddenHttpException('Не переданы параметры для присвоения заказам');
        }

        return [
            'success' => true,
            'order' => $order
        ];
    }


    public function actionAjaxSetConfirm($id, $set = true)
    {
        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);
        if($set == true) {
            //$order->setField('is_confirmed', true);
            $order->is_confirmed = true;
            if(empty($order->first_confirm_click_time)) {
                $order->first_confirm_click_time = time();
            }else {
                $order->last_confirm_click_time = time();
            }
            if(empty($order->first_confirm_clicker_id)) {
                $order->first_confirm_clicker_id = Yii::$app->user->id;
            }else {
                $order->last_confirm_clicker_id = Yii::$app->user->id;
            }

            if(!$order->save(false)) {
                throw new ErrorException('Ошибка сохранения заказа');
            }


        }else {
            $order->setField('is_confirmed', false);
        }

        return [
            'success' => true,
        ];
    }


    public function actionAjaxSetTimeSat($id, $set = true)
    {
        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);


        $fact_trip_transport = $order->factTripTransport;
        if($fact_trip_transport == null) {
            throw new ForbiddenHttpException('Не найдено транспортное средство для посадки');
        }
        if($order->places_count > 0) {
            if($set == true) {
                $fact_trip_transport->used_places_count += $order->places_count;
            }else {
                $fact_trip_transport->used_places_count -= $order->places_count;
            }
            $fact_trip_transport->setField('used_places_count', $fact_trip_transport->used_places_count);
        }


        if($set == true) {

            // в неподтвержденное т/с нельзя посадить клиента
            if($fact_trip_transport->confirmed != 1) {
                throw new ForbiddenHttpException('Транспортное средство не подтверждено');
            }

            // проверка что в т/с достаточно свободных мест
            if($fact_trip_transport->factSatPlacesCount + $order->places_count > $fact_trip_transport->transport->places_count) {
                throw new ForbiddenHttpException('Недостаточно мест в т/с');
            }

            $order->setField('time_sat', time());
            $order->setField('time_satter_user_id', Yii::$app->user->identity->id);

            // для режима работы рейса "Стандартный режим" (т.е. мобильное приложение = 0), подтверждение посадки не используется
            // значит если т/с уже отправлена, значит заказ надо тоже делать отправленным с машиной.
            if($order->trip != null && $order->trip->use_mobile_app == 0) {
                // echo "fact_trip_transport:<pre>"; print_r($fact_trip_transport); echo "</pre>";
                if(!empty($fact_trip_transport->date_sended)) {
                    // отправляем заказ
                    $order = Order::find()->where(['id' => $order->id])->one();
                    $order->sendWithTransport();
                }
            }

            DispatcherAccounting::createLog('order_sat_to_transport', $order->id); // логируем Посадку в машину

        }else {

            $order->setField('time_sat', null);
            $order->setField('time_satter_user_id', null);
            $order->setField('confirmed_time_sat', null);
            $order->setField('confirmed_time_satter_user_id', null);
            DispatcherAccounting::createLog('order_unsat_from_transport', $order->id); // логируем Высадка из машины

            // если клиент уже оплатил (или деньги за заказ получены и чек был создан), то нужно вернуть деньги и выдать чек возврата
            if(!empty($order->paid_time)) {
                $order->cancelPay();
            }
        }


        $driver = $fact_trip_transport->driver;
        if($driver != null) {

            // отправляем сообщение на все возможные устройства пользователя
            $magic_code = '';
            $aMesData = [
                'message_type' => 'command',
                'message' => 'updateYandexMap',
            ];
            if($driver->magicDevice != null) {
                $magic_code = $driver->magicDevice->code;
                //SocketDemon::sendOutDeviceMessageInstant($magic_code, 'command', 'updateYandexMap');
                SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
            }
            if(!empty($driver->device_code) && $driver->device_code != $magic_code) {
                //SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'command', 'updateYandexMap');
                SocketDemon::sendOutDeviceMessageInstant($driver->device_code, $aMesData);
            }
        }


        return [
            'success' => true,
        ];
    }


    public function actionAjaxSetTimeSatConfirm($id)
    {
        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);

        $fact_trip_transport = $order->factTripTransport;
        if($fact_trip_transport == null) {
            throw new ForbiddenHttpException('Не найдено транспортное средство для посадки');
        }
//        if($fact_trip_transport->status_id != 0 || intval($fact_trip_transport->date_sended) > 0) {
//             throw new ForbiddenHttpException('Транспортное средство уже было отправлено или отменено');
//        }
        // в неподтвержденное т/с нельзя посадить клиента
        if($fact_trip_transport->confirmed != 1) {
            throw new ForbiddenHttpException('Транспортное средство не подтверждено');
        }

        if(empty($order->time_sat)) {
            throw new ForbiddenHttpException('В транспортное средство не было посадки без подтверждения');
        }

        $order->setField('confirmed_time_sat', time());
        $order->setField('confirmed_time_satter_user_id', Yii::$app->user->identity->id);
        //DispatcherAccounting::createLog('order_sat_to_transport', $order->id); // логируем Посадку в машину

        if($fact_trip_transport != null && !empty($fact_trip_transport->date_sended)) {

            // отправляем заказ
            $order->sendWithTransport();

            /*
            // отправляем заказ
            $order_status = OrderStatus::getByCode('sent');
            Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $order_status->id . ', status_setting_time=' . time() . ', updated_at='.time().' WHERE id = '.$order->id)->execute();

            // если у всех т/с на рейсе закончились места, то устанавливаем рейсу  has_free_places = 0
            // проверяем есть ли свободные места в отправленных т/с
            if($order->trip_id > 0) {

                $sended_trip_transports = TripTransport::find()
                    ->where(['trip_id' => $order->trip_id])
                    ->all();

                $has_free_places = false;
                foreach ($sended_trip_transports as $trip_transport) {
                    $transport = $trip_transport->transport;
                    if ($transport->places_count > $trip_transport->factKZMPlacesCount) {
                        $has_free_places = true;
                        break;
                    }
                }

                if($has_free_places == false) {
                    $order->trip->setField('has_free_places', 0);
                }
            }
            */
        }

        if($fact_trip_transport != null) {
            $driver = $fact_trip_transport->driver;
            if($driver != null) {
//                if($driver->magicDevice != null) {
//                    SocketDemon::sendOutDeviceMessageInstant($driver->magicDevice->code, 'command', 'updateYandexMap');
//                }elseif(!empty($driver->device_code)) {
//                    SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'command', 'updateYandexMap');
//                }

                // отправляем сообщение на все возможные устройства пользователя
                $magic_code = '';
                $aMesData = [
                    'message_type' => 'command',
                    'message' => 'updateYandexMap',
                ];
                if($driver->magicDevice != null) {
                    $magic_code = $driver->magicDevice->code;
                    //SocketDemon::sendOutDeviceMessageInstant($magic_code, 'command', 'updateYandexMap');
                    SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
                }
                if(!empty($driver->device_code) && $driver->device_code != $magic_code) {
                    //SocketDemon::sendOutDeviceMessageInstant($driver->device_code, 'command', 'updateYandexMap');
                    SocketDemon::sendOutDeviceMessageInstant($driver->device_code, $aMesData);
                }
            }
        }

        return [
            'success' => true,
        ];
    }


    public function actionAjaxConfirmSelectedTransport($id)
    {
        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);

        if(empty($order->time_confirm) || $order->is_confirmed != 1 || empty($order->fact_trip_transport_id)) {
            throw new ForbiddenHttpException('Чтобы установить КЗМ должно быть выбрано ВРПТ, подтверждено ВРПТ и выбрано факт. т/с');
        }

        $order->setField('confirm_selected_transport', true);

        return [
            'success' => true,
        ];
    }


    public function actionAjaxSetTimeConfirmSort($id, $value) {

        Yii::$app->response->format = 'json';

        $order = $this->findModel($id);
        $order->setField('time_confirm_sort', $value);

        return [
            'success' => true,
        ];
    }


    public function actionCancellationReasonOrders($date)
    {

        $searchModel = new OrderSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['OrderSearch']['date'] = $date;
        $dataProvider = $searchModel->cancellationSearch($queryParams);

        return $this->render('cancellation-reason-orders', [
            'date' => $date,
            //'cancellation_reason' => $cancellation_reason,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxGetRadioGroup1() {

        Yii::$app->response->format = 'json';

        $date = Yii::$app->request->post('date');
        $time_confirm = Yii::$app->request->post('time_confirm');
        //$street_id_from = intval(Yii::$app->request->post('street_id_from'));
        //$point_id_from = intval(Yii::$app->request->post('point_id_from'));
        $yandex_point_from_name = Yii::$app->request->post('yandex_point_from_name');

        $trip_id = intval(Yii::$app->request->post('trip_id'));
        //$trip_transports = TripTransport::find()->where(['trip_id' => $trip_id, 'status_id' => 0])->all();

        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $date)) {
            $date = strtotime($date);
        }

        if(preg_match('/^[0-9]{2} : [0-9]{2}$/i', $time_confirm)) {
            $hour_minute = explode(':', $time_confirm);
            $time_confirm = $date + trim($hour_minute[0])*3600 + trim($hour_minute[1])*60;
        }

        $order = new Order();
        $order->date = $date;
        $order->time_confirm = $time_confirm;
        //$order->street_id_from = $street_id_from;
        //$order->point_id_from = $point_id_from;
        $order->yandex_point_from_name = $yandex_point_from_name;
        $order->trip_id = $trip_id;

        return [
            'radio_group_1' => $order->radioGroup1
        ];
    }


    public function actionAjaxSetCheckedClientLastOrders($order_temp_identifier, $order_id)
    {
        Yii::$app->response->format = 'json';

        DispatcherAccounting::createLog('order_checked_last_orders', $order_id, 0, 0, '', $order_temp_identifier);

        return [
            'success' => true,
        ];
    }


    public function actionAjaxGetPenaltyForm($order_id) {

        Yii::$app->response->format = 'json';

        $order = Order::find()->where(['id' => $order_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }
        $order->scenario = 'penalty';

        $client = $order->client;
        if($client == null) {
            throw new ForbiddenHttpException('Клиент не найден');
        }

        if($order->load(Yii::$app->request->post()) && $order->validate())
        {
            $order->has_penalty = 1;
            $order->penalty_time = time();
            $order->penalty_author_id = Yii::$app->user->id;

            if(!$order->save()) {
                throw new ErrorException('Не удалось сохранить заказ');
            }

            //$client->penalty = $client->penalty + 1;
            //$client->setField('penalty', $client->penalty);
            $client->current_year_penalty = $client->current_year_penalty + 1;
            $client->setField('current_year_penalty', $client->current_year_penalty);

            return [
                'success' => true,
            ];
        }

        return [
            'html' => $this->renderAjax('penalty-form', [
                'order' => $order,
            ]),
            'client' => $client
        ];
    }


//    public function actionAjaxGetClientextBlock()
//    {
//        Yii::$app->response->format = 'json';
//
//        return [
//            'success' => true,
//            'html' => \app\widgets\ClientextWidget::widget()
//        ];
//    }


    public function actionAjaxGetClientExtList()
    {
        Yii::$app->response->format = 'json';

        //$clientexts = ClientExt::find()->where(['status' => 'created'])->all();
        $clientexts_orders = Order::find()
            ->where(['status_id' => 0])
            ->andWhere(['>', 'external_id', 0])
            ->andWhere(['external_type' => 'client_site'])
            ->all();

        return [
            'success' => true,
            'html' => $this->renderPartial('@app/widgets/views/clientext/list', [
                //'clientexts' => $clientexts,
                'clientexts_orders' => $clientexts_orders
            ])
        ];
    }


    public function actionAjaxSendOrderToTrip($order_id) {

        Yii::$app->response->format = 'json';

        $order = Order::find()->where(['id' => $order_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        $order->sendToTrip();

        return [
            'success' => true
        ];
    }

    public function actionAjaxSendOrderToTransport($order_id) {

        Yii::$app->response->format = 'json';

        $order = Order::find()->where(['id' => $order_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        // $order->sendToTrip();

        return [
            'success' => true
        ];
    }


    public function actionAjaxPayAndMakeCheck($order_id) {

        Yii::$app->response->format = 'json';

        $order = Order::find()->where(['id' => $order_id])->one();
        if($order == null) {
            throw new ForbiddenHttpException('Заказ не найден');
        }

        $fact_trip_transport = $order->factTripTransport;
        if($fact_trip_transport == null) {
            throw new ForbiddenHttpException('Не найдено транспортное средство связанное с заказом');
        }

        // делаем заказ оплаченным
        $order->cash_received_time = time();
        $order->setField('cash_received_time', $order->cash_received_time);
        $order->setPay();

        // передаем сообщение в браузеры
        SocketDemon::sendOutBrowserMessageInstant('/trip/trip-orders', ['trip_id' => $order->trip_id], 'updateTripOrdersPage()', []);


        // отправляем сообщение на все возможные устройства пользователя
        if($fact_trip_transport != null) {
            $driver = $fact_trip_transport->driver;
            if($driver != null) {

                // отправляем сообщение на все возможные устройства пользователя
                $magic_code = '';
                $aMesData = [
                    'message_type' => 'command',
                    'message' => 'updateYandexMap',
                ];
                if($driver->magicDevice != null) {
                    $magic_code = $driver->magicDevice->code;
                    SocketDemon::sendOutDeviceMessageInstant($magic_code, $aMesData);
                }
                if(!empty($driver->device_code) && $driver->device_code != $magic_code) {
                    SocketDemon::sendOutDeviceMessageInstant($driver->device_code, $aMesData);
                }
            }
        }

        return [
            'success' => true
        ];
    }


    public function actionTest() {

        echo date('d.m.Y H:i', 1506978000);

//        $role_alias = Yii::$app->session->get('role_alias');
//        echo 'role_alias='.$role_alias;
//
//        return $this->render('test');
    }
}
