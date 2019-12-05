<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\components\Helper;

/**
 * Форма для создания списка тестовых заказов
 */

class CreateTestOrders extends \yii\base\Model
{
    public $date;           // дата - date - int
    public $direction_id;   // направление - direction_id
    public $trip_id;        // рейс - trip_id
    public $orders_count;   // кол-во заказов - orders_count - int
    public $transports_count = 0; // кол-во машин на рейсе - int



    public function rules()
    {
        return [
            [['date', 'direction_id', 'trip_id', 'orders_count'], 'required'],
            [['orders_count'], 'integer', 'min' => 1, 'max' => 50],
            [['transports_count'], 'integer', 'min' => 0, 'max' => 5],
            [['direction_id', 'trip_id', 'orders_count'], 'integer'],
            [['date'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'date' => 'Дата',
            'direction_id' => 'Направление',
            'trip_id' => 'Рейс',
            'orders_count' => 'Количество заказов',
            'transports_count' => 'Машин на рейсе (можно не заполнять)'
        ];
    }

    public function generateOrders() {

        // можно еще вставить input-поле "добавить на рейс манин - кол-во"
        if($this->transports_count > 0) {
            for($i = 0; $i < $this->transports_count; $i++) {
                $aRandomTripTransports[] = $this->createRandomTripTransportWithDriver();
            }
        }

        for($i = 0; $i < $this->orders_count; $i++) {

            $order = new Order();
            $order->scenario = 'writedown_button_create';

            //$operator = User::find()->where(['id' => 1])->one(); // выбирается произвольно из имеющихся операторов
            $operator = $this->getRandomOperator();

            $random_client = $this->getRandomClient();
            $order->client_id = $random_client->id; // произвольный
            $order->client_name = $random_client->name; // аналогично

            $random_order_status = $this->getRandomOrderStatus();
            $order->status_id = $random_order_status->id; // с большей вероятностью должен быть 'created', с небольшой вероятностью 'canceled'

            // если статус = canceled, тогда:
            if($random_order_status->code == 'canceled')
            {
                $random_cancellation_reason = $this->getRandomCancellationReason();
                $order->cancellation_reason_id = $random_cancellation_reason->id; // произвольна
                $order->cancellation_click_time = time();
                $order->cancellation_clicker_id = $operator->id;
                $order->canceled_by = 'operator';
            }

            $order->date = $this->date;
            $order->direction_id =  $this->direction_id;
            $order->trip_id = $this->trip_id;

            // если это ветки кода без яндекс-точек, то
//            if(!isset($order->yandex_point_from_name)) {
//                $order->street_id_from = $this->getRandomStreetIdFrom($order); // выбирается произвольно в соответствии с направлением/городом
//                $order->point_id_from = $this->getRandomPointIdFrom($order); // выбирается произвольно в соответствии с направлением/городом
//                $order->street_id_to = $this->getRandomStreetIdTo($order); // выбирается произвольно в соответствии с направлением/городом
//                $order->point_id_to = $this->getRandomPointIdTo($order); // выбирается произвольно в соответствии с направлением/городом
//            }else { // заполняем яндекс-точки на основе существующих точек в базе

                $yandex_point_from = $this->getRandomYandexPointFrom($order);
                $yandex_point_to = $this->getRandomYandexPointTo($order);

                $order->yandex_point_from_id = $yandex_point_from->id;
                $order->yandex_point_from_name = $yandex_point_from->name;
                $order->yandex_point_from_lat = $yandex_point_from->lat;
                $order->yandex_point_from_long = $yandex_point_from->long;
                $order->yandex_point_to_id = $yandex_point_to->id;
                $order->yandex_point_to_name = $yandex_point_to->name;
                $order->yandex_point_to_lat = $yandex_point_to->lat;
                $order->yandex_point_to_long = $yandex_point_to->long;
//            }


            $order->time_air_train_departure = $this->getRandomTimeAirTrainDeparture($order); // заполняется только если точка отправления - критическая
            $order->time_air_train_arrival = $this->getRandomTimeAirTrainArrival($order); // заполняется только если точка прибытия - критическая

            $order->informer_office_id = $this->getRandomInformerOfficeId(); // как правило нет, иногда выбирается произвольное значение

            $order->places_count = rand(0, 4); // от 0 до 4-х. В зависимости от этого количества должны генерироваться:
            if($order->places_count == 0) {
                $order->is_not_places = 1; // как правило нет, иногда да
            }
            $order->student_count = $this->getRandomStudentCount($order); // читай выше...
            $order->child_count = $this->getRandomChildCount($order); // читай выше...
            $order->bag_count =  rand(0, $order->places_count); // произвольное значение от 0 до places_count
            $order->suitcase_count =  rand(0, $order->places_count); // произвольное значение от 0 до places_count
            $order->oversized_count =  rand(0, $order->places_count); // произвольное значение от 0 до places_count
            // prize_trip_count - не заполняется, должно автоматически обновляться
            $order->comment = '';

            // произвольно выбирается один из телефонов, и заполняется произвольными цифрами
            $add_phone_num = rand(0, 3);
            if($add_phone_num == 1) {
                $order->additional_phone_1 = $this->getRandomPhone();
            }else {
                $order->additional_phone_1 = '';
            }
            if($add_phone_num == 2) {
                $order->additional_phone_2 = $this->getRandomPhone();
            }else {
                $order->additional_phone_2 = '';
            }
            if($add_phone_num == 3) {
                $order->additional_phone_3 = $this->getRandomPhone();
            }else {
                $order->additional_phone_3 = '';
            }


            // устанавливается в зависимости от того есть ли машины на рейсе, если есть то одна из них
            if($this->transports_count > 0) {
                $random_fact_trip_transport = $aRandomTripTransports[rand(0, count($this->transports_count) - 1)];
                $order->fact_trip_transport_id = $random_fact_trip_transport->id;
                $order->fact_trip_transport_car_reg = $random_fact_trip_transport->transport->car_reg; // аналогично

                if(empty($order->cancellation_reason_id) && rand(0, 1) == 1) {
                    $order->confirm_selected_transport = true;// КЗМ - выбирается произвольно да или нет
                }
                if(
                    empty($order->cancellation_reason_id)
                    && $order->confirm_selected_transport == true
                    && rand(0, 1) == 1
                    && $random_fact_trip_transport->confirmed == 1
                ) {
                    $order->time_sat = time();// заполняется произвольно если в поле "добавить на рейс машин" есть количество
                }

            }


            if(rand(1, 5) == 1) {
                $order->use_fix_price = true; // произвольно редко устанавливается
                $order->price = rand(1, 1000);
            }
            // в остальных случаях должна рассчитываться при создании/сохранении заказа
            //$order->price =

            //$order->time_confirm_sort = ''; // не заполняю
            //$order->first_writedown_click_time = ''; // не заполняю

            // Группа radio-кнопок "Подтвердить сейчас" / "Не подтверждать"
            $order->radio_confirm_now = (bool) rand(1, 2);
            if($order->radio_confirm_now == 1) {

                if($order->cancellation_reason_id > 0) {
                    $order->is_confirmed = 0;
                }else {
                    $order->is_confirmed = 1;
                    $order->time_confirm = $this->getRandomTimeConfirm($order);  // ВРПТ (Время подтверждения)
                    $order->first_confirm_click_time = time();// Время первичного нажатия кнопки Подтвердить
                    $order->first_confirm_clicker_id = $operator->id;
                }

                // Первая группа radio-кнопок
                $order->radio_group_1 = rand(1, 2);
                // Третья группа radio-кнопок
                $order->radio_group_3 = rand(1, 2);

            }else { // 2
                $order->is_confirmed = 0;

                // Вторая группа radio-кнопок
                $order->radio_group_2 = rand(1, 2);
                // Третья группа radio-кнопок
                $order->radio_group_3 = rand(1, 2);
            }

            //Время первичного нажатия кнопки Записать
            $order->first_writedown_click_time = time();
            // Пользователь (диспетчер) впервые нажавший кнопку Записать
            $order->first_writedown_clicker_id = $operator->id;


            // связанный заказ:  relation_order_id - не заполняю
            // created_at и updated_at - не заполняю, заполняться автоматически
            $order->is_test = true;
            if(!$order->save()) {
                $errors = $order->getErrors();
                echo "функция generateOrders(), ошибки:<pre>"; print_r($errors); echo "</pre>";
                exit();
            }
        }

        return true;
    }

    // произвольный оператор

    private function createRandomTripTransportWithDriver() {

        $trip_transport = new TripTransport();

        $trip = Trip::find()->where(['id' => $this->trip_id])->one();
        $used_trip_transports = $trip->tripTransports;

        $aUsedDriversId = [];
        foreach($used_trip_transports as $used_trip_transport) {
            $aUsedDriversId[] = $used_trip_transport->driver_id;
        }

        $transports = $trip->freeDirectionDateTransports;
        if(count($transports) == 0) {
            throw new ForbiddenHttpException('Свободный транспорт не найден');
        }
        $aTransports = [];
        foreach($transports as $transport) {
            $aTransports[] = $transport;
        }
        $random_transport = $aTransports[rand(0, count($aTransports) - 1)];
        $trip_transport->transport_id = $random_transport->id;

        $drivers = Driver::find()->where(['NOT IN', 'id', $aUsedDriversId])->all();
        $aDrivers = [];
        foreach($drivers as $driver) {
            $aDrivers[] = $driver;
        }
        $random_driver = $aDrivers[rand(0, count($aDrivers) - 1)];
        $trip_transport->driver_id = $random_driver->id;

        $trip_transport->trip_id = $this->trip_id;
        $trip_transport->status_id = 0;

        if(rand(0, 1) == 1) {
            $operator = $this->getRandomOperator();
            $trip_transport->confirmed = 1;
            $trip_transport->confirmed_date_time = time();
            $trip_transport->confirmed_user_id = $operator->id;
        }

        if(!$trip_transport->save()) {
            $errors = $trip_transport->getErrors();
            echo "ошибки добавления машины:<pre>"; print_r($errors); echo "</pre>";
            //throw new ForbiddenHttpException('Не удалось добавить транспорт на рейс');
        }

        return $trip_transport;
    }

    // произвольный клиент

    private function getRandomOperator() {

        $aOperatorRoles = ArrayHelper::map(UserRole::find()->where(['alias' => ['editor', 'manager']])->all(), 'id', 'id');

        $operators = User::find()->where(['role_id' => $aOperatorRoles])->all();
        if(count($operators) == 0) {
            throw new ForbiddenHttpException('Операторы не найдены');
        }

        $aOperators = [];
        foreach($operators as $operator) {
            $aOperators[] = $operator;
        }

        return $aOperators[rand(0, count($operators) - 1)];
    }

    // с большей вероятностью должен быть 'created', с небольшой вероятностью 'canceled'

    private function getRandomClient() {

        $sql = 'SELECT id FROM '.Client::tableName().' ORDER BY RAND() LIMIT 1';
        $aRes = \Yii::$app->db->createCommand($sql)->queryAll();

        return Client::find()->where(['id' => $aRes[0]['id']])->one();
    }

    // произвольна

    private function getRandomOrderStatus() {

        if(rand(1, 5) == 1) { // canceled
            return OrderStatus::getByCode('canceled');
        }else { // created
            return OrderStatus::getByCode('created');
        }
    }


    // выбирается произвольно в соответствии с направлением/городом

    private function getRandomCancellationReason() {

        $cancellation_reasons = OrderCancellationReason::find()->all();
        $aCancellationReasons = [];
        foreach($cancellation_reasons as $cancellation_reason) {
            $aCancellationReasons[] = $cancellation_reason;
        }

        return $aCancellationReasons[rand(0, count($aCancellationReasons) - 1)];
    }
    // выбирается произвольно в соответствии с направлением/городом

    /*
    private function getRandomStreetIdFrom($order) {

        if($order->direction == null) {
            throw new ForbiddenHttpException('Направление не найдено direction_id='.$order->direction_id);
        }
        $city_streets = Street::find()->where(['city_id' => $order->direction->city_from])->all();
        $aCityStreets = [];
        foreach($city_streets as $city_street) {
            $aCityStreets[] = $city_street;
        }

        return $aCityStreets[rand(0, count($aCityStreets) - 1)]->id;
    }

    // выбирается произвольно в соответствии с направлением/городом
    private function getRandomPointIdFrom($order) {

        $city_points = Point::find()->where(['city_id' => $order->direction->city_from])->all();
        $aCityPoints = [];
        foreach($city_points as $city_point) {
            $aCityPoints[] = $city_point;
        }

        return $aCityPoints[rand(0, count($aCityPoints) - 1)]->id;
    }

    // выбирается произвольно в соответствии с направлением/городом
    private function getRandomStreetIdTo($order) {

        $city_streets = Street::find()->where(['city_id' => $order->direction->city_to])->all();
        $aCityStreets = [];
        foreach($city_streets as $city_street) {
            $aCityStreets[] = $city_street;
        }

        return $aCityStreets[rand(0, count($aCityStreets) - 1)]->id;
    }

    // заполняется только если точка отправления - критическая
    private function getRandomPointIdTo($order) {

        $city_points = Point::find()->where(['city_id' => $order->direction->city_to])->all();
        $aCityPoints = [];
        foreach($city_points as $city_point) {
            $aCityPoints[] = $city_point;
        }

        return $aCityPoints[rand(0, count($aCityPoints) - 1)]->id;
    }
*/

    private function getRandomYandexPointFrom($order) {

        if($order->direction == null) {
            throw new ForbiddenHttpException('Направление не найдено direction_id='.$order->direction_id);
        }
        $city_yandex_points = YandexPoint::find()->where(['city_id' => $order->direction->city_from])->all();
        $aYandexPoints = [];
        foreach($city_yandex_points as $city_yandex_point) {
            $aYandexPoints[] = $city_yandex_point;
        }

        return $aYandexPoints[rand(0, count($aYandexPoints) - 1)];
    }

    private function getRandomYandexPointTo($order) {

        if($order->direction == null) {
            throw new ForbiddenHttpException('Направление не найдено direction_id='.$order->direction_id);
        }
        $city_yandex_points = YandexPoint::find()->where(['city_id' => $order->direction->city_to])->all();
        $aYandexPoints = [];
        foreach($city_yandex_points as $city_yandex_point) {
            $aYandexPoints[] = $city_yandex_point;
        }

        return $aYandexPoints[rand(0, count($aYandexPoints) - 1)];
    }

    // заполняется только если точка прибытия - критическая
    private function getRandomTimeAirTrainDeparture($order) {

        //$point_from = Point::find()->where(['id' => $order->point_id_from])->one();
        $point_from = YandexPoint::find()->where(['id' => $order->yandex_point_from_id])->one();
        if($point_from->critical_point  == 1) {
            $hour = rand(0, 23);
            if($hour < 10) {
                return '0'.$hour.':00';
            }else {
                return $hour.':00';
            }
        }else {
            return '';
        }
    }

    // ВРПТ - время подтверждения - произвольное, но близкое к времени 1-й точки рейса
    // - есть диапозон доступного времени подтверждения

    private function getRandomTimeAirTrainArrival($order) {

        //$point_to = Point::find()->where(['id' => $order->point_id_to])->one();
        $point_to = YandexPoint::find()->where(['id' => $order->yandex_point_to_id])->one();
        if($point_to->critical_point  == 1) {
            $hour = rand(0, 23);
            if($hour < 10) {
                return '0'.$hour.':00';
            }else {
                return $hour.':00';
            }
        }else {
            return '';
        }
    }

    // как правило нет, иногда выбирается произвольное значение

    private function getRandomInformerOfficeId() {

        if(rand(0, 3) == 1) {
            $informer_offices = InformerOffice::find()->all();
            $aInformerOffices = [];
            foreach($informer_offices as $informer_office) {
                $aInformerOffices[] = $informer_office;
            }
            return $aInformerOffices[rand(0, count($aInformerOffices) - 1)]->id;

        }else {
            return 0;
        }
    }

    private function getRandomStudentCount($order){

        if($order->places_count > 0) {
            return rand(0, $order->places_count);
        }else {
            return 0;
        }
    }

    private function getRandomChildCount($order) {

        if($order->places_count > 0 && ($order->places_count - $order->student_count) > 0) {
            return rand(0, ($order->places_count - $order->student_count));
        }else {
            return 0;
        }
    }


    // телефонный номер из произвольных цифр
    private function getRandomPhone() {

        // +7-345-345-3456
        return '+7-'.rand(0,9).rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    }

    private function getRandomTimeConfirm($order) {

        $start_time = Helper::convertHoursMinutesToSeconds($order->trip->start_time) - 1800;
        $end_time = Helper::convertHoursMinutesToSeconds($order->trip->end_time) + 7200;

        return rand($start_time, $end_time);
    }
}


