<?php

namespace app\controllers;

use app\components\Helper;
use app\models\Driver;
use app\models\OrderPassenger;
use app\models\Passenger;
use app\models\Transport;
use Yii;
use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Trip;
use app\models\TripTransport;
use yii\helpers\ArrayHelper;
use app\models\DispatcherAccounting;
use yii\web\UnauthorizedHttpException;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;


class TripTransportController extends Controller
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


    /*
     * Функция возвращает форму "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxGetAddCarsForm($trip_id)
    {
        Yii::$app->response->format = 'json';

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

		return [
			'success' => true,
            'html' => $this->renderAjax('add-cars-form', [
                'trip' => $trip,
                'trip_transports' => $trip->tripTransports,
			])
		];
    }

    /*
     * Функция возвращает список машин рейса для SelectWidget-элемента или элемента картика или т.п.
     */
    public function actionAjaxGetTransportsNames($trip_id = 0, $accountability = -1) {

        Yii::$app->response->format = 'json';

        $format = Yii::$app->getRequest()->post('format');

        if($trip_id > 0) {
            $trip = Trip::findOne($trip_id);
            if ($trip == null) {
                throw new ForbiddenHttpException('Рейс не найден');
            }

            $search = Yii::$app->getRequest()->post('search');
            $selected_transports_ids = Yii::$app->getRequest()->post('selected_transports_ids');
            if (empty($selected_transports_ids)) {
                $selected_transports_ids = [];
            }
            $transports = $trip->freeDirectionDateTransports;

        }else {
            $search = Yii::$app->getRequest()->post('search');
            $selected_transports_ids = Yii::$app->getRequest()->post('selected_transports_ids');
            if (empty($selected_transports_ids)) {
                $selected_transports_ids = [];
            }

            $transports = Transport::find()
                ->where(['active' => 1])
                ->orderBy(['sh_model' => SORT_ASC, 'CONVERT(car_reg,SIGNED)' => SORT_ASC])
                ->all();
        }

        $aTransports1 = [];
        $aTransports2 = [];
        foreach($transports as $transport) {
            if($transport->accountability == true) {
                $aTransports1[] = $transport;
            }else {
                $aTransports2[] = $transport;
            }
        }
        $transports = array_merge($aTransports1, $aTransports2);

        $out['results'] = [];
        foreach($transports as $transport) {

            if($format == 'name3') {
                $text = $transport->name3;
            }else {
                $text = $transport->car_reg_places_count;
            }

            if($accountability > -1 && $transport->accountability == false) {
                $text .= ' - неподотчетна';
            }

            if($search != '') {
                if(mb_stripos($text, $search, 0, 'UTF-8') !== false && !in_array($transport->id, $selected_transports_ids)) {
                    $out['results'][] = [
                        'id' => $transport->id,
                        'text' => $text,
                        'accountability' => $transport->accountability,
                        'transports' => array_flip($selected_transports_ids)
                    ];
                }
            }else {
                if(!in_array($transport->id, $selected_transports_ids)) {
                    $out['results'][] = [
                        'id' => $transport->id,
                        'text' => $text,
                        'accountability' => $transport->accountability,
                        'transports' => array_flip($selected_transports_ids)
                    ];
                }
            }
        }

        return $out;
    }


    /*
     * Функция возвращает список водителей рейса для SelectWidget-элемента в форме "Добавить транспорт к рейсу"
     */
    public function actionAjaxGetDriversNames($trip_id = 0) {

        Yii::$app->response->format = 'json';

        $search = Yii::$app->getRequest()->post('search');
        $selected_transport_id = intval(Yii::$app->getRequest()->post('selected_transport_id'));
        //$selected_drivers_ids = Yii::$app->getRequest()->post('selected_drivers_ids');
        $selected_driver_id = Yii::$app->getRequest()->post('selected_driver_id');

        //$accountability = Yii::$app->getRequest()->post('accountability');


        if (empty($selected_transport_id)) {
            $selected_driver_id = [];
        }

        if($trip_id > 0) {
            $trip = Trip::findOne($trip_id);
            if ($trip == null) {
                throw new ForbiddenHttpException('Рейс не найден');
            }

            $drivers = Trip::getEmptyDriversOnDirectionOfDate($trip_id, $selected_transport_id);

        }else {

            $drivers = Driver::find()
                ->where(['active' => 1])
                ->orderBy([
                    'fio' => SORT_ASC,
                    //'accountability' => SORT_DESC
                ])
                ->all();

            $drivers = Trip::_getDriversList($drivers, $selected_transport_id, $selected_driver_id);
        }

        $out['results'] = [];

        $aDrivers1 = [];
        $aDrivers2 = [];
        foreach($drivers as $driver) {
            if($driver->accountability == true) {
                $aDrivers1[] = $driver;
            }else {
                $aDrivers2[] = $driver;
            }
        }
//        $drivers = $aDrivers1 + $aDrivers2;
        $drivers = array_merge($aDrivers1, $aDrivers2);

        foreach($drivers as $driver) {

            $text = $driver->fio;

            if($search != '') {
                if(
                    mb_stripos($text, $search, 0, 'UTF-8') !== false
                    //&& !in_array($driver->id, $selected_drivers_ids)
                    && $driver->id != $selected_driver_id
                ) {
                    $out['results'][] = [
                        'id' => $driver->id,
                        'text' => $text,
                    ];
                }
            }else {
                //if(!in_array($driver->id, $selected_drivers_ids)) {
                if($driver->id != $selected_driver_id) {
                    $out['results'][] = [
                        'id' => $driver->id,
                        'text' => $text,
                    ];
                }
            }
        }

        return $out;
    }



    /*
     * Функция возвращает незаполненную строку "транспорт-водитель" для формы "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxGetAddCarTr($trip_id)
    {
        Yii::$app->response->format = 'json';

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        $trip_transport = new TripTransport();

        return [
            'success' => true,
            'tr_html' => $this->renderPartial('_add-cars-form-row', [
                'trip' => $trip,
                'trip_transport' => $trip_transport,
                //'transport_list' => ['' => '---'] + ArrayHelper::map(Trip::getEmptyTransportsOnDirectionOfDate($trip_id), 'id', 'car_reg_places_count'),
                //'driver_list' => ['' => '---'] + ArrayHelper::map(Trip::getEmptyDriversOnDirectionOfDate($trip_id), 'id', 'fio'),
            ])
        ];
    }

    /*
     * Функция сохраняет данных формы "Привязка транспортного средства к рейсу"
     */
    public function actionAjaxSaveCarsForm($trip_id)
    {
        Yii::$app->response->format = 'json';

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        if($trip->updatePostTripTransports(Yii::$app->request->post())) {
            return ['success' => true];
        }else {
            return ['success' => false];
        }
    }

    /*
     * Удаление записи из trip_transport
     */
    public function actionAjaxDelete($id)
    {
        Yii::$app->response->format = 'json';

        $trip_transport = TripTransport::findOne($id);
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Не найден trip_transport_id');
        }

        $trip_transport->delete();
        DispatcherAccounting::createLog('trip_transport_delete');// логируем Снятие т/с с рейса

        return [
            'success' => true,
        ];
    }

    /*
     * Удаление записи из trip_transport
     */
    public function actionDeleteTripTransport($trip_transport_id){

        $model = TripTransport::findOne($trip_transport_id);
        if($model) {
            $model->delete();
            DispatcherAccounting::createLog('trip_transport_delete');// логируем Снятие т/с с рейса
        }

        return 'ok';
    }


    public function actionGetSendForm($id) {

        $trip_transport = TripTransport::findOne($id);
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Не найден trip_transport_id');
        }

        return $this->renderPartial('trip-transport-send-form', [
            'trip_transport' => $trip_transport
        ]);
    }


    public function actionAjaxSend($id)
    {
        Yii::$app->response->format = 'json';

        $trip_transport = TripTransport::findOne($id);
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Не найден trip_transport_id');
        }


        if($trip_transport->send()) {

            // DispatcherAccounting::createLog('trip_transport_send'); // логируем Отправку т/с

            return [
                'success' => true,
            ];
        }else {
            return [
                'success' => false,
                'errors' => ['Не удалось отправить машину']
            ];
        }
    }


    public function actionShowCarInfo($trip_transport_id){

        $trip_transport = TripTransport::findOne($trip_transport_id);
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver_list = (!empty($trip_transport->driver_id) ? [$trip_transport->driver_id => $trip_transport->driver->fio] : ['' => '---']);
        $driver_list = $driver_list + ArrayHelper::map(Trip::getEmptyDriversOnDirectionOfDate($trip_transport->trip_id, $trip_transport->transport_id, $trip_transport->driver_id), 'id', 'fio');

	    return $this->renderAjax('showCarInfo', [
            'trip_transport' => $trip_transport,
            'driver_list' => $driver_list
        ]);
    }


    public function actionChangeConfirm($trip_transport_id, $confirmed){
    
        $trip_transport = TripTransport::findOne($trip_transport_id);

        $trip_transport->setConfirm($confirmed);
        if(!empty($confirmed)) {
            DispatcherAccounting::createLog('trip_transport_confirm'); // логируем Подтверждение тс
        }

        return json_encode([
            'success' => true,
            'confirmed' => $trip_transport->confirmed
        ]);
    }

    
    public function actionChangeDriverOrCar($trip_transport_id, $driver_id=null, $transport_id=null){

        $model = TripTransport::findOne($trip_transport_id);
        if($model){
            if($driver_id == $model->oldAttributes['driver_id']) {
                return 'error1'; // не нужно пересохранять старого водителя
            }

            $model->deleteAccessKey();

            if($driver_id !== null && is_numeric($driver_id)){
                $model->driver_id = (int)$driver_id;
            }

            if($transport_id !== null && is_numeric($transport_id)){
                $model->transport_id = (int)$transport_id;
            }

            if($model->save()){
                DispatcherAccounting::createLog('trip_transport_change_driver'); // логируем Смену водителя

                return 'ok';
            } else {
                //echo "<pre>"; print_r($model->getErrors()); echo "</pre>";
                return false;
            }

        } else {
            return 'error3';
        }
    }


    public function actionGetEmptyDrivers($trip_id=null, $direction_id=null, $date=null, $transport_id=null,$selected_driver_id=null){
        $result = [];

        $empty_drivers = Trip::getEmptyDriversOnDirectionOfDate($trip_id, $transport_id, $selected_driver_id);

        $list = [];
        foreach($empty_drivers as $item){
            $list[] = ['id'=>$item->id, 'value'=>$item->fio];
        }

        return json_encode(['success'=>true, 'list'=>$list]);

    }


    public function actionAjaxGetTransportPosition($id) {

        Yii::$app->response->format = 'json';

        $trip_transport = TripTransport::find()->where(['id' => $id])->one();
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Транспорт на рейсе не найден');
        }

        $transport = $trip_transport->transport;
        if($transport == null) {
            throw new ForbiddenHttpException('Транспорт не найден');
        }

        $driver = $trip_transport->driver;
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель не найден');
        }

        $user = $driver->user;
        if($user == null) {
            throw new ForbiddenHttpException('Пользователь связанный с водителем не найден');
        }

        return [
            'transport_name' => $transport->name2,
            'transport_color' => $transport->color,
            'transport_model' => $transport->model,
            'transport_car_reg' => $transport->car_reg,
            'driver_name' => $driver->fio,
            'lat' => $user->lat,
            'long' => $user->long
        ];
    }


    public function actionExportToCsv($id) {


        if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            throw new UnauthorizedHttpException('Нет доступа');
        }

        $trip_transport = TripTransport::find()->where(['id' => $id])->one();
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Машина на рейсе не найдена');
        }

        // найдем все заказы машины
        $fact_orders = $trip_transport->factOrdersWithoutCanceled;


        $allModels = [];

        foreach($fact_orders as $order) {
            $allModels[] = [ // тел. ...12345
                'places_count' => $order->places_count,
                'client_name' => $order->client_name,
                'phone' => $order->client_id > 0 ? 'тел. ...'.substr(str_replace('-', '', $order->client->mobile_phone), -5) : ''
            ];
        }

        $exporter = new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $allModels,
            ]),
            'columns' => [
                [
                    'attribute' => 'places_count',
                ],
                [
                    'attribute' => 'client_name',
                ],
                [
                    'attribute' => 'phone',
                ],
            ],
        ]);


        //$exporter->export()->saveAs('/path/to/file.csv');

        $trip = $trip_transport->trip;
        $file =  $trip->direction->sh_name.' '.$trip->name.' от '.date('d.m.Y', $trip->date).' для машины '.$trip_transport->transport->car_reg;

        return $exporter->export()->send($file.'.csv');
    }

    /*
    public function actionExportToCsv($id) {


        if(!in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            throw new UnauthorizedHttpException('Нет доступа');
        }

        $trip_transport = TripTransport::find()->where(['id' => $id])->one();
        if($trip_transport == null) {
            throw new ForbiddenHttpException('Машина на рейсе не найдена');
        }


//        ФИО клиента
//        Название яндекс-точки откуда
//        Название яндекс-точки куда
//        Без места
//        Количество мест всего
//        Цена

//        новые колонки:

//        surname (фамилия)
//        name
//        patronymic
//        birthday - 1986-08-29
//        docType - пустой
//        docNumber - пустой
//        documentAdditionalInfo - пустой
//        departPlace - место отбытия - точка откуда с городом
//        arrivePlace - место прибытия - точка куда с городом
//        routeType - пустой
//        departDate - дата заказа: 2016-10-06T12:30Z
//        departDateFact - пустой
//        citizenship - Гражданство
//        gender - М или Ж
//        recType - пустой
//        rank - пустой
//        operationType - пустой
//        operatorId - пустой
//        placeId - пустой
//        route - пустой
//        places - всегда 1
//        buyDate - дата+время отправки рейса: 2016-08-22T01:00Z
//        termNumOrSurname - пустой
//        arriveDate - дата прибытия - по логике время отправления + время в пути - пустой оставлю
//        arriveDateFact - пустой
//        grz - пустой
//        model - пустой
//        registerTimeIS - пустой
//        operatorVersion - пустой

//        $query = (new \yii\db\Query())
//            ->select('id as `id заказа`, client_name as `Имя клиента`, yandex_point_from_name as `Точка откуда`, yandex_point_to_name as `Точка куда`, is_not_places as `Без места`, places_count as `Кол-во мест`, price as `Цена`')
//            ->from('order')
//            ->where(['fact_trip_transport_id' => $id]);
//
//        $exporter = new CsvGrid([
//            'dataProvider' => new ActiveDataProvider([
//                'query' => $query
//            ]),
//        ]);

        $aModels = [];

        // найдем все заказы машины
        $fact_orders = $trip_transport->factOrdersWithoutCanceled;

        // для каждого заказа найдем всех пассажиров
        $aOrdersIds = ArrayHelper::map($fact_orders, 'id', 'id');
        $orders_passengers = OrderPassenger::find()->where(['order_id' => $aOrdersIds])->all();
        //$aOrdersPassengers = ArrayHelper::map($orders_passengers, 'order_id', 'passenger_id');

        $passengers = Passenger::find()->where(['id' => ArrayHelper::map($orders_passengers, 'passenger_id', 'passenger_id')])->all();
        $aPassengers = ArrayHelper::index($passengers, 'id');



        // соберем итоговый массив данных пассажиров

        $aOrders = ArrayHelper::index($fact_orders, 'id');

        $aOrdersPassengers = [];
        foreach($orders_passengers as $orders_passenger) {
            if(isset($aPassengers[$orders_passenger->passenger_id])) {
                $aOrdersPassengers[$orders_passenger->order_id][$orders_passenger->passenger_id] = $aPassengers[$orders_passenger->passenger_id];
            }
        }

//        echo "aOrders:<pre>"; print_r($aOrders); echo "</pre>";
//        echo "aOrdersPassengers:<pre>"; print_r($aOrdersPassengers); echo "</pre>";
//        exit;

        $allModels = [];
        foreach($aOrdersPassengers as $order_id => $aPassengers) {

            $order = $aOrders[$order_id];


            foreach($aPassengers as $passenger_id => $passenger) {

                $allModels[] = [
                    'surname' => $passenger->surname,
                    'name' => $passenger->name,
                    'patronymic' => $passenger->patronymic,
                    'birthday' => date("Y-m-d", $passenger->date_of_birth), // 1986-08-29
                    'docType' => !empty($passenger->series) ? 0 : '-',
                    'docNumber' => $passenger->series.$passenger->number,
                    'documentAdditionalInfo' => '',
                    'departPlace' => $order->direction->cityFrom->name, // место отбытия - город отбытия
                    'arrivePlace' => $order->direction->cityTo->name, // место прибытия - город прибытия
                    'routeType' => '',
                    'departDate' => date("c", $order->date + Helper::convertHoursMinutesToSeconds($order->trip->start_time)), // дата+время отправки рейса: 2016-10-06T12:30Z
                    'departDateFact' => '',
                    'citizenship' => $passenger->citizenship, // Гражданство
                    'gender' => ($passenger->gender == 0 ? 'Ж' : 'М'),
                    'recType' => '',
                    'rank' => '',
                    'operationType' => '',
                    'operatorId' => '',
                    'placeId' => '',
                    'route' => '',
                    'places' => 1,
                    'buyDate' => date("c", $order->date + Helper::convertHoursMinutesToSeconds($order->trip->start_time)), // дата+время отправки рейса: 2016-08-22T01:00Z
                    'termNumOrSurname' => '',
                    'arriveDate' => '',
                    'arriveDateFact' => '',
                    'grz' => '',
                    'model' => '',
                    'registerTimeIS' => '',
                    'operatorVersion' => '',
                ];
            }
        }


        $exporter = new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $allModels,
//                    [
//                        [
//                            'name' => 'some name',
//                            'price' => '9879',
//                        ],
//                        [
//                            'name' => 'name 2',
//                            'price' => '79',
//                        ],
//                ],
            ]),
            'columns' => [
//                [
//                    'attribute' => 'name',
//                ],
//                [
//                    'attribute' => 'price',
//                    'format' => 'decimal',
//                ],
                [
                    'attribute' => 'surname',
                ],
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'patronymic',
                ],
                [
                    'attribute' => 'birthday',
                ],
                [
                    'attribute' => 'docType',
                ],
                [
                    'attribute' => 'docNumber',
                ],
                [
                    'attribute' => 'documentAdditionalInfo',
                ],
                [
                    'attribute' => 'departPlace',
                ],
                [
                    'attribute' => 'arrivePlace',
                ],
                [
                    'attribute' => 'routeType',
                ],
                [
                    'attribute' => 'departDate',
                ],
                [
                    'attribute' => 'departDateFact',
                ],
                [
                    'attribute' => 'citizenship',
                ],
                [
                    'attribute' => 'gender',
                ],
                [
                    'attribute' => 'recType',
                ],
                [
                    'attribute' => 'rank',
                ],
                [
                    'attribute' => 'operationType',
                ],
                [
                    'attribute' => 'operatorId',
                ],
                [
                    'attribute' => 'placeId',
                ],
                [
                    'attribute' => 'route',
                ],
                [
                    'attribute' => 'places',
                ],
                [
                    'attribute' => 'buyDate',
                ],
                [
                    'attribute' => 'termNumOrSurname',
                ],
                [
                    'attribute' => 'arriveDate',
                ],
                [
                    'attribute' => 'arriveDateFact',
                ],
                [
                    'attribute' => 'grz',
                ],
                [
                    'attribute' => 'model',
                ],
                [
                    'attribute' => 'registerTimeIS',
                ],
                [
                    'attribute' => 'operatorVersion',
                ],
            ],
        ]);


        //$exporter->export()->saveAs('/path/to/file.csv');

        $trip = $trip_transport->trip;
        $file =  $trip->direction->sh_name.' '.$trip->name.' от '.date('d.m.Y', $trip->date).' для машины '.$trip_transport->transport->car_reg;

        return $exporter->export()->send($file.'.csv');
    }
    */


}
