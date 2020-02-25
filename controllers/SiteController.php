<?php

namespace app\controllers;

use app\components\sms\providers\Sms4bProvider;
use app\components\sms\SmsNotification;
use app\models\Access;
use app\models\Call;
use app\models\CallCase;
use app\models\CallEvent;
use app\models\ChatMessage;
use app\models\Client;
use app\models\ClientServer;
use app\models\DayReportTransportCircle;
use app\models\DayReportTransportCircleSearch;
use app\models\Driver;
use app\models\LiteboxOperation;
use app\models\NotaccountabilityTransportReport;
use app\models\OperatorBeelineSubscription;
use app\models\Order;
use app\models\OrderStatus;
use app\models\SocketDemon;
use app\models\Transport;
use app\models\TransportWaybill;
use app\models\TripTransport;
use app\models\UploadForm;
use app\widgets\IncomingOrdersWidget;
use Yii;
use yii\base\ErrorException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\UserRole;
use app\models\Trip;
use app\components\Helper;
use app\models\Direction;
use app\models\DayReportTripTransport;
use app\models\DayReportTripTransportSearch;
use app\models\TripTransportSearch;
use app\models\DispatcherAccounting;
use yii\web\ForbiddenHttpException;
use app\models\Setting;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($date = null)
    {
        $today_unixtime = strtotime(date('d.m.Y', time()));
        $tomorrow = $today_unixtime + 86400;
        $selected_unixdate = (!empty($date) ? strtotime($date) : $today_unixtime);

        /*
        $setting = Setting::find()->where(['id' => 1])->one();
        if(Yii::$app->session->get('role_alias') == 'editor') {

            if($setting == null || intval($setting->create_orders_yesterday) == 0) {
                $yesterday = $today_unixtime - 86400;
                if ($selected_unixdate < $yesterday) {
                    throw new ForbiddenHttpException('Доступ запрещен1');
                }
            }

        }elseif(Yii::$app->session->get('role_alias') == 'manager') {
            if ($selected_unixdate < $today_unixtime) {
                throw new ForbiddenHttpException('Доступ запрещен2');
            }

        }elseif(in_array(Yii::$app->session->get('role_alias'), ['graph_operator', 'warehouse_turnover'])) {

            if (($selected_unixdate < $today_unixtime) || ($selected_unixdate > $tomorrow)) {
                throw new ForbiddenHttpException('Доступ запрещен3');
            }

        }elseif(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin', ])) {
            throw new ForbiddenHttpException('Доступ запрещен4');
        }
        */

        if ($selected_unixdate < $today_unixtime) {
            if(!Access::hasUserAccess('past', 'page_part')) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }elseif($selected_unixdate > $tomorrow) {
            if(!Access::hasUserAccess('future', 'page_part')) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }

        return $this->render('index', [
            //'selected_unixdate' => $selected_unixdate, // не используется
            //'user' => Yii::$app->user->identity,
            'aDirections' => Direction::getDirectionsTrips($selected_unixdate)
        ]);
    }

    /*
     * Функция возвращает html-код блока с рейсами
     */
    public function actionAjaxGetDirectionsTripsBlock($date = null)
    {
        Yii::$app->response->format = 'json';

        $selected_unixdate = (!empty($date) ? strtotime($date) : strtotime(date('d.m.Y', time())));

        return [
            'success' => true,
            'html' => $this->renderPartial('/site/directions-trips-block', [
                'aDirections' => Direction::getDirectionsTrips($selected_unixdate),
                'view' => 'trip_list'
            ]),
        ];
    }

    /*
     * Функция возвращает html чата
     */
    public function actionAjaxGetChat() {

        Yii::$app->response->format = 'json';

        $is_open = (Yii::$app->getRequest()->post('is_open') === 'true');

//        return $this->renderAjax('chat', [
//            'is_open' => $is_open
//        ]);

        return [
            'html' => $this->renderAjax('chat', [
                'is_open' => $is_open
            ])
        ];
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->rememberMe = 1;

        if(count($_GET) > 0) {
            $username = array_keys($_GET)[0];
        }else {
            $username = '';
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->user->loginByCookie()) {

//            if($model->operator_subscription_id > 0) {
//                $operator_subscription = OperatorBeelineSubscription::find()->where(['id' => $model->operator_subscription_id])->one();
//                if($operator_subscription == null) {
//                    throw new ForbiddenHttpException('Подписка с id='.$model->operator_subscription_id.' не найдена');
//                }
//                if($operator_subscription->operator_id > 0) {
//                    throw new ForbiddenHttpException('СИП-Аккаунт уже занят');
//                }
//
//                if($operator_subscription->createAtsSubscription()) {
//                    $operator_subscription->setField('operator_id', $model->user->id);
//                }else {
//                    throw new ErrorException('Не удалось создать подписку');
//                }
//
//                if(!$operator_subscription->setStatus('OFFLINE')) {
//                    throw new ErrorException('Не удалось установить офлайн-статус');
//                }
//            }

            if(Yii::$app->session->get('role_alias') == 'warehouse_turnover') {
                $url = Yii::$app->getRequest()->getBaseUrl() . '/storage';
                return Yii::$app->getResponse()->redirect($url);

            }elseif(Yii::$app->session->get('role_alias') == 'graph_operator') {
                    $url = Yii::$app->getRequest()->getBaseUrl().'/waybill/transport-waybill/list';
                    return Yii::$app->getResponse()->redirect($url);
            }else {
                return $this->goHome();
            }
        }


        $this->layout = 'login';
        return $this->render('login', [
            'model' => $model,
            'username' => $username
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->response->format = 'json';

        $user_id = Yii::$app->user->id;

        $user = User::findOne($user_id);
        if($user != null) {

            $user->logoutByCookie();

            // код на время тестирования подписок
            //if(Yii::$app->user != null) {
                $operator_subscription = OperatorBeelineSubscription::find()
                    ->where(['operator_id' => $user_id])
                    ->one();
                if ($operator_subscription != null) {

                    if(!$operator_subscription->isExistInAts()) {
                        throw new ForbiddenHttpException('Сбой подписки обнаружен при выходе');
                    }

                    $operator_subscription->setStatus('OFFLINE'); // в АТС статус устанавливается неподписке, а СИПу

                    if($operator_subscription->deleteFromAts()) {
                        throw new ForbiddenHttpException('Подписка агента завершена штатно user_id='.$user_id.' operator_subscription_id='.$operator_subscription->id);
                    }else {
                        throw new ForbiddenHttpException('Сбой подписки обнаружен при выходе');
                    }
                }else {
                    throw new ForbiddenHttpException('В CRM нет подписки агента для удаления');
                }
            //}

        }


        return $this->goHome();
    }

    /*
     * Функция возвращает строку времени для часов в верхнем меню
     *
     * @return string
     */
    public function actionGetAjaxTime() {

        Yii::$app->response->format = 'json';

        return [
            'success' => true,
            'time' => Helper::getMainDate(time(), 1),
            'restart_page' => date('H:i', time()) == '00:00',
        ];
    }

    /*
     * Функция возвращает html для модального окна отчета текущего дня
     */
    public function actionAjaxGetDayReport($date) {

        $searchModel = new DayReportTransportCircleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $date);

        $unixdate = strtotime($date);
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            $prev_date = date('d.m.Y', $unixdate - 86400);
            $next_date = date('d.m.Y', $unixdate + 86400);
            $title = '<a href="?date=' . $prev_date . '&day-report" id="day-report-arrow-left">&larr; </a> Текущий отчет дня ' . $date . ' (' . Helper::getWeekDay($unixdate) . ')' . ' <a href="?date=' . $next_date . '&day-report" id="day-report-arrow-right">&rarr; </a>';
        }else {
            $title = 'Текущий отчет дня ' . $date . ' ('. Helper::getWeekDay($unixdate) . ')';
        }

        Yii::$app->response->format = 'json';
        return [
            'html' => $this->renderAjax('day-report-transport-circle-grid', [
                'date' => $date,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]),
            'title' => $title
        ];
    }


    public function actionAjaxSaveChatMessage($dialog_id = 0) {

        Yii::$app->response->format = 'json';

        $post = Yii::$app->request->post();


        $chat_message = new ChatMessage();
        if($dialog_id == 0) {
            $max_dialog_message = ChatMessage::find()->orderBy(['dialog_id' => SORT_DESC])->one();
            if($max_dialog_message == null) {
                $chat_message->dialog_id = 1;
            }else {
                $chat_message->dialog_id = $max_dialog_message->dialog_id + 1;
            }
        }else {
            $chat_message->dialog_id = $dialog_id;
        }

        $chat_message->created_at = time();
        $chat_message->user_id = Yii::$app->user->id;
        if($dialog_id == 0) {
            $strLifetime = Yii::$app->request->post('lifetime');
            $aLifetime = explode(':', $strLifetime);
            $intLifetime = 3600*$aLifetime[0] + 60*$aLifetime[1];
            $chat_message->expiration_time = $chat_message->created_at + $intLifetime;
            $chat_message->to_the_begining = (int)($post['to_begining'] === 'true');
        }else {

            $first_dialog_message = ChatMessage::find()
                ->where(['dialog_id' => $dialog_id])
                ->orderBy(['id' => SORT_ASC])
                ->one();
            $chat_message->expiration_time = $first_dialog_message->expiration_time;
        }
        $chat_message->message = $post['message'];

        //echo '<pre>'; print_r($chat_message); echo '</pre>';
        if(!$chat_message->save()) {
            return [
                'success' => false,
                'errors' => $chat_message->getErrors()
            ];
        } else {
            return [
                'success' => true,
            ];
        }
    }


    public function actionAjaxGetEjsvForm() {

        Yii::$app->response->format = 'json';


        $user = Yii::$app->user->identity;

        return [
            'operator_name' => $user->lastname.' '.$user->firstname,
            'html' => $this->renderAjax('ejsv-form')
        ];
    }

    public function actionAjaxSearchTransportWaybills($date, $transport_id, $driver_id) {

        Yii::$app->response->format = 'json';

        $aTransportWaybills = [];
        $transport_waybills = TransportWaybill::find()
            ->where(['date_of_issue' => strtotime($date)])
            ->andWhere(['transport_id' => $transport_id])
            ->andWhere(['driver_id' => $driver_id])
            ->all();
        if(count($transport_waybills) > 0) {
            //$aTransportWaybills = ArrayHelper::map($transport_waybills, 'number', 'number');
            foreach ($transport_waybills as $transport_waybill) {
                $aTransportWaybills[$transport_waybill->number] = $transport_waybill->number .' от '.date('d/m/Y', $transport_waybill->date_of_issue);
            }
        }

        return [
            'transport_waybills' => $aTransportWaybills,
            'date' =>  strtotime($date)
        ];
    }

    public function actionAjaxSearchDayReportTransportCircle($pl_number, $date, $transport_id, $driver_id) {

        Yii::$app->response->format = 'json';

        $aStartTripsNames = []; // рейсы для текущего дня и текущей машины отправляющихся из города базирования
        $aEndTripsNames = []; // рейсы для текущей машины и дней(текущий и следующий) отправляющихся из города небазирования

        $transport_waybill = TransportWaybill::find()
            ->where(['number' => $pl_number])
            ->andWhere(['date_of_issue' => strtotime($date)])
            ->andWhere(['transport_id' => $transport_id])
            ->andWhere(['driver_id' => $driver_id])
            ->one();
        $transport = Transport::find()->where(['id' => $transport_id])->one();
        $date_of_issue = strtotime($date);

        // определим направление "базирование" - это направление в котогом город отправления равен городу базирования транспорта
        if(empty($transport->base_city_id)) {
            throw new ForbiddenHttpException('Город базирования у машины не найден');
        }
        $base_direction = Direction::find()->where(['city_from' => $transport->base_city_id])->one();
        if($base_direction == null) {
            throw new ForbiddenHttpException('Город базирования не найден в направлениях');
        }


        $base_trips = Trip::find()
            ->where(['date' => $date_of_issue])
            ->andWhere(['direction_id' => $base_direction->id])
            ->all();
        $aBaseTrips = ArrayHelper::index($base_trips, 'id');
        $aBaseTripsIds = ArrayHelper::map($base_trips, 'id', 'id');

        $base_trip_transports = TripTransport::find()
            ->where(['transport_id' => $transport_id])
            ->andWhere(['trip_id' => $aBaseTripsIds])
            ->andWhere(['status_id' => 1])
            ->all();
        foreach($base_trip_transports as $trip_transport) {
            $trip = $aBaseTrips[$trip_transport->trip_id];
            $aStartTripsNames[$trip_transport->id] = ($trip->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip->name;
        }


        $notbase_direction = Direction::find()->where(['!=', 'city_from', $transport->base_city_id])->one();
        if($notbase_direction == null) {
            throw new ForbiddenHttpException('Город небазирования не найден в направлениях');
        }

        $notbase_trips = Trip::find()
            ->where(['date' => [$date_of_issue, $date_of_issue + 86400]])
            ->andWhere(['direction_id' => $notbase_direction->id])
            ->all();
        $aNotBaseTrips = ArrayHelper::index($notbase_trips, 'id');
        $aNotBaseTripsIds = ArrayHelper::map($notbase_trips, 'id', 'id');


        $notbase_trip_transports = TripTransport::find()
            ->where(['transport_id' => $transport_id])
            ->andWhere(['trip_id' => $aNotBaseTripsIds])
            ->andWhere(['status_id' => 1])
            ->all();
        foreach($notbase_trip_transports as $trip_transport) {
            $trip =  $aNotBaseTrips[$trip_transport->trip_id];
            $aEndTripsNames[$trip_transport->id] = ($trip->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip->name . ' ('.date('d.m.Y', $trip->date).')';
        }


        return [
            'success' => true,
            'html' => $this->renderAjax('ejsv-search-day-report-transport-circle', [
                // 'transport_waybill' => $transport_waybill,
                'trip_transport_start' => ($transport_waybill != null ? $transport_waybill->trip_transport_start : 0),
                'trip_transport_end' => ($transport_waybill != null ? $transport_waybill->trip_transport_end : 0),
                'aStartTripsNames' => $aStartTripsNames,
                'aEndTripsNames' => $aEndTripsNames,
            ]),
        ];
    }


    public function actionAjaxSearchNotaccountabilityTransportCircles($date_start, $date_end, $transport_id, $driver_id) {

        Yii::$app->response->format = 'json';

        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);
        $transport = Transport::find()->where(['id' => $transport_id])->one();

        // определим направление "базирование" - это направление в котогом город отправления равен городу базирования транспорта
        if(empty($transport->base_city_id)) {
            throw new ForbiddenHttpException('Город базирования у машины не найден');
        }
        $base_direction = Direction::find()->where(['city_from' => $transport->base_city_id])->one();
        if($base_direction == null) {
            throw new ForbiddenHttpException('Город базирования не найден в направлениях');
        }

        $base_trips = Trip::find()
            ->where(['date' => $date_start])
            ->andWhere(['direction_id' => $base_direction->id])
            ->all();
        $day_report_transport_circles = DayReportTransportCircle::find()->where(['base_city_trip_id' => ArrayHelper::map($base_trips, 'id', 'id')])->all();

        return [
            'success' => true,
            'html' => $this->renderAjax('ejsv-search-notaccountability-transport-circle', [
                'date_start' => $date_start,
                'date_end' => $date_end,
                'day_report_transport_circles' => $day_report_transport_circles,
            ]),
        ];
    }

//    public function actionAjaxCheckPlNumber($pl_number) {
//
//        Yii::$app->response->format = 'json';
//
//        $transport_waybill = TransportWaybill::find()->where(['number' => $pl_number])->one();
//
//        if($transport_waybill == null) {
//            return [
//                'success' => false,
//            ];
//        }else {
//
//            $driver = Driver::find()->where(['id' => $transport_waybill->driver_id])->one();
//            $transport = Transport::find()->where(['id' => $transport_waybill->transport_id])->one();
//
//            return [
//                'success' => true,
//                //'pl_id' => $transport_waybill->id,
//                'driver_id' => $transport_waybill->driver_id,
//                'driver_name' => ($driver != null ? $driver->fio : ''),
//                'transport_id' => $transport_waybill->transport_id,
//                'transport_name' => ($transport != null ? $transport->name3 : ''),
//                'date' => date('d.m.Y', $transport_waybill->date_of_issue)
//            ];
//        }
//    }

    public function actionGetCheckData($pl_number = 0, $trip_transport_start = 0, $trip_transport_end = 0, $transport_id, $driver_id, $date) {

        $transport = Transport::find()->where(['id' => $transport_id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver = Driver::find()->where(['id' => $driver_id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }


        if($trip_transport_start > 0) {
            $trip_transport_from = TripTransport::find()->where(['id' => $trip_transport_start])->one();
            $trip_from = $trip_transport_from->trip;
        }else {
            $trip_from = null;
        }

        if($trip_transport_end > 0) {
            $trip_transport_to = TripTransport::find()->where(['id' => $trip_transport_end])->one();
            $trip_to = $trip_transport_to->trip;
        }else {
            $trip_to = null;
        }

        // $waybill->number, $waybill->date_of_issue
        return $this->renderAjax('ejsv-check-form', [
            'pl_number' => $pl_number,
            'pl_date' => $date,
            'transport' => $transport,
            'driver' => $driver,
            'trip_from' => $trip_from,
            'trip_to' => $trip_to,
        ]);
    }


    public function actionGetCheckDataNotaccountabilityTransport($transport_id, $driver_id, $day_report_transport_circle_id) {

        $transport = Transport::find()->where(['id' => $transport_id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver = Driver::find()->where(['id' => $driver_id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $circle = DayReportTransportCircle::find()->where(['id' => $day_report_transport_circle_id])->one();
        if($circle == null) {
            throw new ForbiddenHttpException('Круг не найден');
        }

        return $this->renderAjax('ejsv-check-notaccountability-transport-form', [
            'transport' => $transport,
            'driver' => $driver,
            'circle' => $circle,
        ]);
    }

    public function actionAjaxSaveNotaccountabilityTransportForm($date_start, $date_end, $transport_id, $driver_id, $day_report_transport_circle_id) {

        Yii::$app->response->format = 'json';

        $notaccountability_transport_report = NotaccountabilityTransportReport::find()
            //->where(['date_start_circle' => strtotime($date_start)])
            //->andWhere(['date_end_circle ' => strtotime($date_end)])
            //->andWhere(['transport_id' => $transport_id])
            //->andWhere(['driver_id' => $driver_id])
            ->where(['day_report_transport_circle_id' => $day_report_transport_circle_id])
            ->one();
        if($notaccountability_transport_report != null) {
            //throw new ForbiddenHttpException('Отчет по неподотчетной машине с такими данными уже существует');
            return [
                'success' => true,
                'notaccountability_transport_report_id' => $notaccountability_transport_report->id
            ];
        }

        $notaccountability_transport_report = new NotaccountabilityTransportReport();
        $notaccountability_transport_report->date_start_circle = strtotime($date_start);
        $notaccountability_transport_report->date_end_circle = strtotime($date_end);
        $notaccountability_transport_report->transport_id = $transport_id;
        $notaccountability_transport_report->driver_id = $driver_id;
        $notaccountability_transport_report->day_report_transport_circle_id = $day_report_transport_circle_id;

        $day_report_transport_circle = DayReportTransportCircle::find()->where(['id' => $day_report_transport_circle_id])->one();
        if($day_report_transport_circle == null) {
            throw new ForbiddenHttpException('Круг рейсов машины не найден');
        }

        $base_day_report_trip_transport = $day_report_transport_circle->baseCityDayReport;
        $notbase_day_report_trip_transport = $day_report_transport_circle->notbaseCityDayReport;
        if($base_day_report_trip_transport != null) {
            $notaccountability_transport_report->trip_transport_start = ($base_day_report_trip_transport->tripTransport != null ? $base_day_report_trip_transport->tripTransport->id : 0);
        }
        if($notbase_day_report_trip_transport != null) {
            $notaccountability_transport_report->trip_transport_end = ($notbase_day_report_trip_transport->tripTransport != null ? $notbase_day_report_trip_transport->tripTransport->id : 0);
        }


        if(!$notaccountability_transport_report->save()) {
            throw new ErrorException('Не удалось сохранить отчет по неподотчетной машине');
        }

        return [
            'success' => true,
            'notaccountability_transport_report_id' => $notaccountability_transport_report->id
        ];
    }

    public function actionGetNotaccountabilityTransportHandoverbbForm($notaccountability_transport_report_id) {

        $notaccountability_transport_report = NotaccountabilityTransportReport::find()->where(['id' => $notaccountability_transport_report_id])->one();
        if($notaccountability_transport_report == null) {
            throw new ForbiddenHttpException('Отчет по неподотчетной машине не найден');
        }

        return $this->renderAjax('ejsv-notaccountability-transport-handoverbb-form', [
            'notaccountability_transport_report' => $notaccountability_transport_report,
        ]);
    }


    public function actionGetHandoverbbForm($pl_number = 0, $trip_transport_start = 0, $trip_transport_end = 0, $transport_id, $driver_id, $date) {

        $transport = Transport::find()->where(['id' => $transport_id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver = Driver::find()->where(['id' => $driver_id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }


        if($pl_number > 0) {

            $transport_waybill = TransportWaybill::find()
                ->where(['number' => $pl_number])
                ->andWhere(['date_of_issue' => strtotime($date)])
                ->andWhere(['transport_id' => $transport_id])
                ->andWhere(['driver_id' => $driver_id])
                ->one();
            if ($transport_waybill == null) {
                // throw new ForbiddenHttpException('Путевой лист не найден');

                $transport_waybill = new TransportWaybill();
                $transport_waybill->number = $pl_number;
                $transport_waybill->date_of_issue = strtotime($date);
                $transport_waybill->transport_id = $transport_id;
                $transport_waybill->driver_id = $driver_id;
                if(!$transport_waybill->save(false)) {
                    throw new ForbiddenHttpException('Не удалось создать путевой лист');
                }

                // создание типовых расходов из выручки с нулевыми значениями
                $transport_waybill->createTypicalExpenses();

            }
            if($trip_transport_start > 0) {
                $transport_waybill->setField('trip_transport_start', $trip_transport_start);
            }else {
                $trip_transport_start = $transport_waybill->trip_transport_start;
            }

            if($trip_transport_end > 0) {
                $transport_waybill->setField('trip_transport_end', $trip_transport_end);
            }else {
                $trip_transport_end = $transport_waybill->trip_transport_end;
            }

            // заново получим обновленные данные по ПЛ чтобы пересчитать
            $transport_waybill = TransportWaybill::find()->where(['id' => $transport_waybill->id])->one();
            $transport_waybill->updateResultFields();// пересчитываем некоторые поля Путевого листа

            $notaccountability_transport_report = null;

        }else {

            throw new ForbiddenHttpException('Не передан номер ПЛ');

//            $transport_waybill = null;
//
//            if($notaccountability_transport_report_id > 0) {
//
//                $notaccountability_transport_report = NotaccountabilityTransportReport::find()->where(['id' => $notaccountability_transport_report_id])->one();
//                if($notaccountability_transport_report == null) {
//                    throw new ForbiddenHttpException('Не удалось найти отчет неподотчетного т/с');
//                }
//
//            }else {
//
//                $notaccountability_transport_report = NotaccountabilityTransportReport::find()
//                    ->where(['date_of_issue' => strtotime($date)])
//                    ->andWhere(['transport_id' => $transport_id])
//                    ->andWhere(['driver_id' => $driver_id])
//                    ->one();
//
//                if($notaccountability_transport_report == null) {
//                    $notaccountability_transport_report = new NotaccountabilityTransportReport();
//                    $notaccountability_transport_report->date_of_issue = strtotime($date);
//                }
//            }

        }


        $basecity_trip_transport = TripTransport::find()->where(['id' => $trip_transport_start])->one();
        $notbasecity_trip_transport = TripTransport::find()->where(['id' => $trip_transport_end])->one();

        if($basecity_trip_transport != null && $notbasecity_trip_transport != null) {
            $day_report_transport_circle = DayReportTransportCircle::find()->where(['base_city_trip_id' => $basecity_trip_transport->trip_id, 'notbase_city_trip_id' => $notbasecity_trip_transport->trip_id])->one();
//            if ($day_report_transport_circle == null) {
//                throw new ForbiddenHttpException('Не найден круг рейсов машины');
//            }
        }else {
            $day_report_transport_circle = null;
        }

        return $this->renderAjax('ejsv-waybill-handoverbb-form', [
            'waybill' => $transport_waybill,
            'notaccountability_transport_report' => $notaccountability_transport_report,
            'day_report_transport_circle' => $day_report_transport_circle,
            'transport' => $transport,
            'driver' => $driver,
        ]);
    }


//    public function actionAjaxRecountPlIndicators($pl_number) {
//
//        Yii::$app->response->format = 'json';
//
//        $transport_waybill = TransportWaybill::find()->where(['number' => $pl_number])->one();
//        if($transport_waybill == null) {
//            throw new ForbiddenHttpException('Путевой лист не найден');
//        }
//
//        $transport_waybill->updateResultFields();// пересчитываем некоторые поля Путевого листа
//
//        return [
//            'success' => true
//        ];
//    }

    public function actionAjaxSaveEjsvData($pl_number = 0, $day_report_transport_circle_id = 0, $transport_id, $driver_id, $date) {

        Yii::$app->response->format = 'json';

        $transport = Transport::find()->where(['id' => $transport_id])->one();
        if($transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver = Driver::find()->where(['id' => $driver_id])->one();
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        if($day_report_transport_circle_id > 0) {
            $day_report_transport_circle = DayReportTransportCircle::find()->where(['id' => $day_report_transport_circle_id])->one();
            if ($day_report_transport_circle == null) {
                throw new ForbiddenHttpException('Не найден круг рейсов машины');
            }
        }else {
            $day_report_transport_circle = null;
        }

        $post = Yii::$app->getRequest()->post();
        $user = Yii::$app->user->identity;
        if(!$user->validatePassword($post['password'])) {
            throw new ForbiddenHttpException('Пароль не правильный');
        }


        if($pl_number > 0) {

            //$transport_waybill = TransportWaybill::find()->where(['number' => $pl_number])->one();
            $transport_waybill = TransportWaybill::find()
                ->where(['number' => $pl_number])
                ->andWhere(['date_of_issue' => strtotime($date)])
                ->andWhere(['transport_id' => $transport_id])
                ->andWhere(['driver_id' => $driver_id])
                ->one();
            if($transport_waybill == null) {
                throw new ForbiddenHttpException('Путевой лист не найден');
            }

            if(isset($post['hand_over_b1'])) {
                // $transport_waybill->hand_over_b1_data = strtotime($post['hand_over_b1_data']);
                $transport_waybill->hand_over_b1_data = time();
                $transport_waybill->hand_over_b1 = $post['hand_over_b1'];
                $transport_waybill->set_hand_over_b1_operator_id = $user->id;
                $transport_waybill->set_hand_over_b1_time = time();
            }else {
                // $transport_waybill->hand_over_b2_data = strtotime($post['hand_over_b2_data']);
                $transport_waybill->hand_over_b2_data = time();
                $transport_waybill->hand_over_b2 = $post['hand_over_b2'];
                $transport_waybill->set_hand_over_b2_operator_id = $user->id;
                $transport_waybill->set_hand_over_b2_time = time();
            }

            if($day_report_transport_circle != null) {
                $base_day_report_trip_transport = $day_report_transport_circle->baseCityDayReport;
                $notbase_day_report_trip_transport = $day_report_transport_circle->notbaseCityDayReport;

                if($base_day_report_trip_transport == null) {
                    throw new ForbiddenHttpException('Рейс из города базирования рейса машины не найден');
                }
                if($notbase_day_report_trip_transport == null) {
                    throw new ForbiddenHttpException('Рейс из промежуточного города рейса машины не найден');
                }
            }else {
                $base_day_report_trip_transport = null;
                $notbase_day_report_trip_transport = null;
            }


            if($base_day_report_trip_transport != null) {
                $transport_waybill->trip_transport_start = ($base_day_report_trip_transport->tripTransport != null ? $base_day_report_trip_transport->tripTransport->id : 0);
            }
            if($notbase_day_report_trip_transport != null) {
                $transport_waybill->trip_transport_end = ($notbase_day_report_trip_transport->tripTransport != null ? $notbase_day_report_trip_transport->tripTransport->id : 0);
            }

            if(!$transport_waybill->save(false)) {
                throw new ForbiddenHttpException('Не удалось сохранить путевой лист');
            }

            // заново получим обновленные данные по ПЛ чтобы пересчитать
            $transport_waybill = TransportWaybill::find()->where(['id' => $transport_waybill->id])->one();
            $transport_waybill->updateResultFields(); // пересчитываем некоторые поля Путевого листа

            return [
                'success' => true,
            ];

        }else { // значит это транспорт без подотчетности

            throw new ForbiddenHttpException('Не передан номер ПЛ');

//            if($notaccountability_transport_report_id > 0) {
//                $notaccountability_transport_report = NotaccountabilityTransportReport::find()->where(['id' => $notaccountability_transport_report_id])->one();
//                if ($notaccountability_transport_report == null) {
//                    throw new ForbiddenHttpException('Не удалось найти отчет неподотчетного т/с');
//                }
//            }else {
//                $notaccountability_transport_report = NotaccountabilityTransportReport::find()
//                    ->where(['date_of_issue' => strtotime($date)])
//                    ->andWhere(['transport_id' => $transport_id])
//                    ->andWhere(['driver_id' => $driver_id])
//                    ->one();
//
//                if($notaccountability_transport_report == null) {
//                    $notaccountability_transport_report = new NotaccountabilityTransportReport();
//                }
//            }
//
//            if(isset($post['hand_over_b1_data'])) {
//                $notaccountability_transport_report->hand_over_b1_data = strtotime($post['hand_over_b1_data']);
//                $notaccountability_transport_report->hand_over_b1 = $post['hand_over_b1'];
//                $notaccountability_transport_report->set_hand_over_b1_operator_id = $user->id;
//                $notaccountability_transport_report->set_hand_over_b1_time = time();
//            }else {
//                $notaccountability_transport_report->hand_over_b2_data = strtotime($post['hand_over_b2_data']);
//                $notaccountability_transport_report->hand_over_b2 = $post['hand_over_b2'];
//                $notaccountability_transport_report->set_hand_over_b2_operator_id = $user->id;
//                $notaccountability_transport_report->set_hand_over_b2_time = time();
//            }
//
//
//            $base_day_report_trip_transport = $day_report_transport_circle->baseCityDayReport;
//            $notbase_day_report_trip_transport = $day_report_transport_circle->notbaseCityDayReport;
//
//
//            if($base_day_report_trip_transport != null) {
//                $notaccountability_transport_report->trip_transport_start = ($base_day_report_trip_transport->tripTransport != null ? $base_day_report_trip_transport->tripTransport->id : 0);
//            }
//            if($notbase_day_report_trip_transport != null) {
//                $notaccountability_transport_report->trip_transport_end = ($notbase_day_report_trip_transport->tripTransport != null ? $notbase_day_report_trip_transport->tripTransport->id : 0);
//            }
//            $notaccountability_transport_report->date_of_issue = strtotime($date);
//            $notaccountability_transport_report->transport_id = $transport->id;
//            $notaccountability_transport_report->driver_id = $driver->id;
//
//            if(!$notaccountability_transport_report->save(false)) {
//                throw new ForbiddenHttpException('Не удалось сохранить отчет неподотчетного т/с');
//            }
//
//            return [
//                'success' => true,
//                'notaccountability_transport_report_id' => $notaccountability_transport_report->id,
//            ];
        }
    }


    public function actionAjaxSaveNotaccountabilityTransportEjsvData($notaccountability_transport_report_id, $hand_over_bb, $password) {

        Yii::$app->response->format = 'json';

        $notaccountability_transport_report = NotaccountabilityTransportReport::find()->where(['id' => $notaccountability_transport_report_id])->one();
        if($notaccountability_transport_report == null) {
            throw new ForbiddenHttpException('Отчет по неподотчетной машине не найден');
        }

        $user = Yii::$app->user->identity;
        if(!$user->validatePassword($password)) {
            throw new ForbiddenHttpException('Пароль не правильный');
        }

        $notaccountability_transport_report->hand_over = $hand_over_bb;
        $notaccountability_transport_report->set_hand_over_operator_id = $user->getId();
        $notaccountability_transport_report->set_hand_over_time = time();
        $notaccountability_transport_report->formula_percent = intval($notaccountability_transport_report->transport->formula->getResult($hand_over_bb));
        if(!$notaccountability_transport_report->save(false)) {
            throw new ErrorException('Не удалось сохранить отчет по неподотчетной машине');
        }

        return [
            'success' => true
        ];
    }


    public function actionTest() {

        //echo 'max_time_short_trip_AK='.Yii::$app->setting->max_time_short_trip_AK;

//        $order = Order::find()->where(['id' => $id])->one();
//        echo "доступен ли кэш-бэк = ".$order->isAllowToUseCashback()."<br />";
//        echo 'полная цена = '.$order->getCalculatePrice(true)."<br />";
//        echo 'кэш-бэк = '.$order->getCalculateUsedCashBack()."<br />";
//        echo 'цена с вычетом кэш-бэка = '.$order->getCalculatePrice()."<br /><br />";

        //echo date("d.m.Y H:i", 1581022800);
        //echo date("d.m.Y H:i:s", time());

        //$order = Order::find()->where(['id' => 206584])->one();
        //echo $order->getCalculateAccrualCashBack($order->price);
        //echo 'AccrualCashBack = '.$order->getCalculateAccrualCashBack($order->price)."<br />";

//        $litebox_operation = LiteboxOperation::find()->where(['order_id' => $order_id])->one();
//        $litebox_operation->checkSellStatusAndUpdate(true);


        // Allowed memory size of 134 217 728 bytes exhausted (tried to allocate 330 235 200 bytes)
        //$order = Order::find()->one();
        //echo "order:<pre>"; print_r($order); echo "</pre>";


//        $aDirections = [1, 2];
//
//        $today_unixtime = strtotime(date('d.m.Y', time()));
//        for($i = 0; $i < 31; $i++) {
//            $data_unixtime = $today_unixtime + $i*86400;
//
//            foreach ($aDirections as $direction_id) {
//                $trip = Trip::find()
//                    ->where(['direction_id' => $direction_id])
//                    ->andWhere(['date' => $data_unixtime])
//                    ->one();
//                if($trip == null) {
//                    Trip::createStandartTripList($data_unixtime, $direction_id);
//                }
//            }
//        }

        /*
        $direction_id = 2;
        $today_unixtime = strtotime(date('d.m.Y', time()));
        $data_unixtime = $today_unixtime + 2*86400;
        $trip = Trip::find()
                ->where(['direction_id' => $direction_id])
                ->andWhere(['date' => $data_unixtime])
                ->one();

        echo "trip:<pre>"; print_r($trip); echo "</pre>";*/
//        echo "".strtotime(date('d.m.Y'))."<br />";
//        echo "дата в заказе = ".(1581195600).'='.date('d.m.Y H:i', 1581195600)."<br />";
//        $d2 = strtotime(date('d.m.Y')) + 2*86400;
//        echo "сегодня + 2дня=".$d2.'='.date('d.m.Y H:i', $d2);

        //echo date('d.m.Y H:i', 1581338121);

        $val = '1admS7';
        // SELECT * FROM `user` WHERE `id`=:qp0
        //echo 'sql='.User::find()->where(['id' => $val])->createCommand()->getSql();
        $user = User::find()->where(['username' => $val])->one();
        echo "user:<pre>"; print_r($user); echo "</pre>";

        /*
        $user = new User();
        $user->username = 'test19fev';
        $user->firstname = 'test';
        $user->lastname = 'fev';
        $user->email = 'test19fev@gmail.com';
        if(!$user->validate()){
            $errors = $user->getErrors();
            echo "errors:<pre>"; print_r($errors); echo "</pre>";

            echo "user:<pre>"; print_r($user); echo "</pre>";

        }else {
            echo "валиден";
        }*/

    }

    public function actionTest2()
    {
        //$order = Order::find()->where(['id' => 216401])->one();

        $or2 = new Order();
        $or2->client_id = 64316;
        $or2->status_id = 1;
        $or2->status_setting_time = time();
        $or2->date = 1582578000;
        $or2->direction_id = 1;
        $or2->yandex_point_from_id = 200;
        $or2->yandex_point_from_name = "Лента, Ленина";
        $or2->yandex_point_from_lat = 54.906945388945;
        $or2->yandex_point_from_long = 52.252717250476;
        $or2->yandex_point_to_id = 2;
        $or2->yandex_point_to_name = "ЖД Центральный";
        $or2->yandex_point_to_lat = 55.789558734666;
        $or2->yandex_point_to_long = 49.102293394195;
        $or2->trip_id = 41078;
        $or2->informer_office_id = 11;
        $or2->is_not_places = 0;
        $or2->places_count = 4;
        $or2->prize_trip_count = 0;
        $or2->time_sat = 1582542146;
        $or2->time_satter_user_id = 45;
        $or2->confirmed_time_sat = 1582542199;
        $or2->confirmed_time_satter_user_id = 49;
        $or2->source_price = 10.00;
        $or2->use_fix_price = 1;
        $or2->price = 10.00;
        $or2->time_confirm = time();
        $or2->time_confirm_auto = time();
        $or2->time_confirm_sort = 15400;
        $or2->time_confirm_delta = 0;
        $or2->is_confirmed = 1;
        $or2->first_opened_form_time = time();
        $or2->first_writedown_click_time = time();
        $or2->first_opened_form_user_id = 45;
        $or2->first_writedown_clicker_id = 45;
        $or2->first_confirm_click_time = time();
        $or2->first_confirm_clicker_id = 45;
        $or2->radio_confirm_now = 1;
        $or2->radio_group_1 = 2;
        $or2->radio_group_3 = 2;
        $or2->confirm_selected_transport = 1;
        $or2->fact_trip_transport_id = 39346;
        $or2->fact_trip_transport_car_reg = 936;
        $or2->has_penalty = 0;
        $or2->created_at = time();

        if(!$or2->save(false)) {
            echo "errors:<pre>"; print_r($or2->getErrors()); echo "</pre>";
        }

//        // делаем заказ оплаченным
//        $order->cash_received_time = time();
//        $order->setField('cash_received_time', $order->cash_received_time);
//        $order->setPay();

    }

    public function actionTest3()
    {
        $operation = LiteboxOperation::find()->where(['id' => 136])->one();
        $operation->checkSellStatusAndUpdate(true);
    }


    public function actionTest4() {

        $headers[] = 'X-MPBX-API-AUTH-TOKEN: cda7719f-1dcc-46f6-937a-23f1d46dcf75';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';

        $ch = curl_init();

        //$record_id = '206636195';
        $record_id = '185595242';

        // curl -X GET --header 'X-MPBX-API-AUTH-TOKEN: 442283ec-c78f-4252-beca-7d5a11168f94'
        // 'https://cloudpbx.beeline.ru/apis/portal/v2/records/206636195/download'
        $url = 'https://cloudpbx.beeline.ru/apis/portal/v2/records/'.$record_id.'/download';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        //echo $result;
        $fp = fopen('/var/www/tobus-yii2/web/records/'.$record_id.'.mp3', "w");
        fwrite($fp, $result);
        fclose($fp);
    }

    public function actionTest5() {

//        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
//        $headers[] = 'Content-Type: application/json; charset=UTF-8';
//
//        $ch = curl_init();
//
//        // $url = 'https://cloudpbx.beeline.ru/apis/portal/abonents/'.$operator_subscription->mobile_ats_login.'/call?phoneNumber='.$phone;
//        $url = '';
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $result = curl_exec($ch);
//        curl_close($ch);

        $headers[] = 'X-MPBX-API-AUTH-TOKEN: '.Yii::$app->params['subscription_id'];
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        //$headers[] = 'Content-Type: application/json; ';

        $data = [
            'inboundNumber' => "9035621779",
            //'inboundNumber' => $phone,
            'extension' => "4002"
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => 'https://cloudpbx.beeline.ru/apis/portal/icr/route',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            //CURLOPT_POSTFIELDS => http_build_query($data)
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        echo "result:<pre>"; print_r($result); echo "</pre>";
    }


    public function actionTestUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return;
            }
        }

        return $this->render('test-upload', ['model' => $model]);
    }


}
