<?php

namespace app\models;

use app\widgets\IncomingOrdersWidget;
use Yii;
use app\models\OrderStatus;
use app\models\Client;
use app\models\Tariff;
use app\models\Point;
use app\models\YandexPoint;
use app\models\Trip;
use app\models\TripTransport;
use app\models\Direction;
use app\models\InformerOffice;
use app\models\Street;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use app\components\Helper;
use app\models\DispatcherAccounting;
use app\models\Setting;


/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $status_id
 * @property integer $cancellation_reason_id
 * @property integer $date
 * @property integer $client_id
 * @property integer $confirm_selected_transport
 * @property integer $fact_trip_transport_id
 * @property integer $point_id_from
 * @property integer $point_id_to
 * @property integer $prize_trip_count
 * @property integer $trip_id
 * @property integer $places_count
 * @property integer $student_count
 * @property integer $child_count
 * @property integer $baggage
 * @property integer $is_not_places
 * @property integer $time_confirm
 * @property string $comment
 * @property string $additional_phone_1
 * @property string additional_phone_2
 * @property string additional_phone_3
 * @property integer $time_sat
 * @property integer $use_fix_price
 * @property string $price
 * @property integer $created_at
 * @property integer $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    const LOYALITY = 5; //  лояльность. Какая поездка в текущей тарифной сетке является призовой

    public $temp_identifier = NULL; // временный идентификатор заказа - нужен пока заказ еще не получил id при сохранении в базе данных

    //public $REPLACE_TO_OTHER_TRIP_MODE = false;
    public $fix_price;
    public $forced = false; // чекбокс "Принудительно"
    public $trip_transport_id; // для установки fact_trip_transport_id из формы создания/редактирования заказа

    public $confirm_click_time;
    public $confirm_clicker_id;


    public $additional_phone_1_new;
    public $additional_phone_2_new;
    public $additional_phone_3_new;

    public static function resetOrders($aOrdersIds) {

        Order::setFields($aOrdersIds, 'is_confirmed', false); // поле "подтвержден" сбрасываем
        Order::setFields($aOrdersIds, 'time_confirm', NULL); // ВРПТ в ноль
        Order::setFields($aOrdersIds, 'time_confirm_auto', NULL);
        //Order::setFields($aOrdersIds, 'time_confirm_sort', NULL); // сортировка связанная с ВРПТ в ноль
        Order::setFields($aOrdersIds, 'confirm_selected_transport', false); // КЗМ в ноль
        Order::setFields($aOrdersIds, 'sync_date', NULL);
        //Order::setFields($aOrdersIds, 'confirm_selected_transport', false); // пересчет цен


        // сообщим браузерам что надо обновить страницу рейсов и обновим окно Активных рейсов
        $update_active_trips = false;
        $orders = Order::find()->where(['id' => $aOrdersIds])->all();
        foreach($orders as $order) {
            if($order->trip_id > 0) {
                SocketDemon::updateMainPages($order->trip_id, $order->date);
            }

            // обновляем окно "Активных рейсов" и красный-желтый у названий рейсов
            $trip = $order->trip;
            if(!$update_active_trips && $trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
                $update_active_trips = true;
            }
        }
        if($update_active_trips) {
            IncomingOrdersWidget::updateActiveTripsModal();
        }

        return true;
    }

    public static function setFields($aOrdersId, $field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_name == 'fact_trip_transport_id') {
            $trip_transport = TripTransport::find()->where(['id' => $field_value])->one();
            if($trip_transport != null && $trip_transport->transport != null) {
                self::setFields($aOrdersId, 'fact_trip_transport_car_reg', $trip_transport->transport->car_reg);
            }else {
                self::setFields($aOrdersId, 'fact_trip_transport_car_reg', '');
            }
        }elseif($field_name == 'client_id') {
            $client = Client::find()->where(['id' => $field_value])->one();
            if($client != null) {
                self::setFields($aOrdersId, 'client_name', $client->name);
            }else {
                self::setFields($aOrdersId, 'client_name', '');
            }
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id IN ('.implode(',', $aOrdersId).')';
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id IN ('.implode(',', $aOrdersId).')';
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id IN ('.implode(',', $aOrdersId).')';
        }


        // сообщим браузерам что надо обновить страницу рейсов и обновим окно Активных рейсов
        $update_active_trips = false;
        $orders = Order::find()->where(['id' => $aOrdersId])->all();
        foreach($orders as $order) {
            if($order->trip_id > 0) {
                SocketDemon::updateMainPages($order->trip_id, $order->date);
            }

            // обновляем окно "Активных рейсов" и красный-желтый у названий рейсов
            $trip = $order->trip;
            if(!$update_active_trips && $trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
                $update_active_trips = true;
            }
        }
        if($update_active_trips) {
            IncomingOrdersWidget::updateActiveTripsModal();
        }


        return Yii::$app->db->createCommand($sql)->execute();
    }

    public static function tableName()
    {
        return 'order';
    }

    public static function getOrderCreateTitle($day_code, $edit_order = false, $trip_id = 0)
    {
        $trip = Trip::findOne($trip_id);
        if($day_code == 'today') {
            if($edit_order) {
                return 'Редактирование заказа на '.Helper::getMainDate(time(), 2).' '.($trip != null ? $trip->name : '').' <input id="order-forced" type="checkbox" /> Принудительно';
            }else {
                return 'Запись заказа на '.Helper::getMainDate(time(), 2).' '.($trip != null ? $trip->name : '');
            }
        }elseif($day_code == 'tomorrow') {
            if($edit_order) {
                return 'Редактирование заказа на '.Helper::getMainDate(time() + 86400, 2).' '.($trip != null ? $trip->name : '').' <input id="order-forced" type="checkbox" /> Принудительно';
            }else {
                return 'Запись заказа на '.Helper::getMainDate(time() + 86400, 2).' '.($trip != null ? $trip->name : '');
            }
        }elseif($day_code == 'another-day') {
            if($edit_order) {
                return 'Редактирование заказа на другой день'.' <input id="order-forced" type="checkbox" /> Принудительно';
            }else {
                return 'Запись заказа на другой день';
            }
        }else {
            return '';
        }
    }

    public function rules()
    {
        return [
            [['date', 'direction_id', 'trip_id',
                /*'point_id_from', 'point_id_to',
                'street_id_from', 'street_id_to',*/
                'yandex_point_from_id', 'yandex_point_from_name', 'yandex_point_from_lat', 'yandex_point_from_long',
                'yandex_point_to_id', 'yandex_point_to_name', 'yandex_point_to_lat', 'yandex_point_to_long',

                'first_writedown_click_time', 'first_writedown_clicker_id', 'penalty_comment',
                'first_opened_form_time', 'first_opened_form_user_id'
            ], 'required'],
            [['yandex_point_from_lat', 'yandex_point_from_long', 'yandex_point_to_lat', 'yandex_point_to_long'], 'number'],
            [['client_position_from_lat', 'client_position_from_long'], 'double'],
            [['places_count'], 'checkPlacesCount', 'skipOnEmpty' => false],
            //['time_confirm', 'timeConfirmCheck', 'skipOnEmpty' => false, 'on' => 'confirm_button_click'],
            ['time_confirm', 'timeConfirmCheck', 'skipOnEmpty' => false, 'on' => 'writedown_button_update'],

            [['direction_id', 'status_id', 'status_setting_time', 'cancellation_reason_id', 'cancellation_click_time', 'cancellation_clicker_id',
                'confirm_selected_transport', 'fact_trip_transport_id', 'prize_trip_count', 'trip_id',
                'is_not_places', 'places_count', 'student_count', 'child_count', 'bag_count', 'suitcase_count', 'oversized_count',
                'informer_office_id',
                // 'time_vpz',
                //'point_id_from', 'point_id_to','street_id_from', 'street_id_to',
                'yandex_point_from_id', 'yandex_point_to_id',
                'first_writedown_click_time', 'first_writedown_clicker_id', 'first_confirm_click_time', 'first_confirm_clicker_id',
                'last_writedown_click_time', 'last_writedown_clicker_id', 'last_confirm_click_time', 'last_confirm_clicker_id',
                'confirm_click_time', 'confirm_clicker_id',
                'radio_confirm_now', 'radio_group_1', 'radio_group_2', 'radio_group_3', 'is_confirmed',
                'has_penalty', 'relation_order_id',
                //'client_ext_id',
                //'client_server_ext_id',
                'external_id',
                'sync_date',
                'penalty_time', 'penalty_author_id', 'first_opened_form_time', 'first_opened_form_user_id',
                //'date_sended'
            ], 'integer'],
            //[['additional_phone_1', 'additional_phone_2', 'additional_phone_3'], 'string', 'max' => 20],
            [['additional_phone_1', 'additional_phone_2', 'additional_phone_3'], 'checkPhone'],
            [['comment', 'yandex_point_from_name', 'yandex_point_to_name', 'penalty_comment'], 'string', 'max' => 255],
            [['temp_identifier'], 'string', 'max' => 32],
            [['date'], 'checkDate'],
            //[['is_mobile'], 'boolean'],
            [['time_confirm', 'time_confirm_auto', 'time_sat', 'created_at', 'updated_at', 'client_id',
                'time_air_train_arrival', 'time_air_train_departure', 'use_fix_price', 'fix_price',
                'price', 'paid_summ', 'paid_time', 'accrual_cash_back', 'penalty_cash_back', 'cash_received_time', 'used_cash_back',
                'forced', 'trip_transport_id', 'time_confirm_sort',
                'fact_trip_transport_car_reg', 'client_name', 'is_test', 'external_type', 'external_created_at', 'is_paid', 'canceled_by'], 'safe'],
        ];
    }

    public function checkDate($attribute, $params)
    {
        if(isset($this->date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->date = strtotime($this->date);   // convent '07.11.2016' to unixtime
        }

        $setting = Setting::find()->where(['id' => 1])->one();
        $today_finish = strtotime(date('d.m.Y'));
        if(($setting == null || intval($setting->create_orders_yesterday) == 0) && $this->date < $today_finish) {
            $this->addError($attribute, 'Нельзя выбрать прошедшую дату');
        }else {
            return true;
        }
    }

    public function checkPhone($attribute)
    {
        if (!preg_match('/^\+7\-[0-9]{3}\-[0-9]{3}\-[0-9]{4}$/', $this->$attribute)) {
            $this->addError($attribute, 'Телефон должен быть в формате +7-***-***-****');
        }else {
            return true;
        }
    }

    public function timeConfirmCheck($attribute, $params)
    {
        // пример пришедшего времени: 12 : 00
        if(empty($this->time_confirm)) {
            //$this->addError($attribute, 'Необходимо заполнить «Время подтверждения».');
            return true;
        }elseif(isset($this->time_confirm) && preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_confirm))
        {
            if(isset($this->date) && !empty($this->date) && isset($this->trip_id) && $this->trip_id > 0)
            {
                if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                    $this->date = strtotime($this->date);
                }

                $trip = $this->trip;

                $start_time = Helper::convertHoursMinutesToSeconds($trip->start_time) - 1800;
                $end_time = Helper::convertHoursMinutesToSeconds($trip->end_time) + 7200;

                $time_confirm = Helper::convertHoursMinutesToSeconds($this->time_confirm);
                if(($time_confirm >= $start_time) && ($time_confirm <= $end_time)) {
                    return true;
                }else {
                    $this->addError($attribute, 'Время подтверждения должно находиться в диапозоне от '.date('H:i', ($start_time - date('Z'))).' до '.date('H:i', $end_time - date('Z')));
                }

            }else {
                $this->addError($attribute, 'Для формирования времени подтверждения транспорта должны быть заполнены поле «Дата» и выбран «Рейс».');
            }

        }else {

            if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                $this->date = strtotime($this->date);
            }

            $trip = $this->trip;
            $time_confirm = $this->time_confirm - $this->date;
            $start_time = Helper::convertHoursMinutesToSeconds($trip->start_time) - 1800;
            $end_time = Helper::convertHoursMinutesToSeconds($trip->end_time) + 7200;

            if(($time_confirm >= $start_time) && ($time_confirm <= $end_time)) {
                return true;
            }else {
                $this->addError($attribute, 'Время подтверждения должно находиться в диапозоне от '.date('H:i', $start_time - date('Z')).' до '.date('H:i', $end_time - date('Z')));
            }
        }

        return true;
    }

    public function checkPlacesCount($attribute, $params)
    {
        if($this->is_not_places == 1) {
            return true;
        }

        if((empty($this->places_count) || $this->places_count == 0) && $this->use_fix_price != true) {
            $this->addError($attribute, 'Необходимо заполнить «Мест».');
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['create_empty'] = [];

        $scenarios['close_client_server_request'] = [
            'status_id',
            'status_setting_time',
            'comment'
        ];

        // сценарий создания заказа при нажатии на кнопку "Записать"
        $scenarios['writedown_button_create'] = [
            'temp_identifier',
            //'client_ext_id',
            //'client_server_ext_id',
            'external_id',
            'external_type',
            'external_created_at',
            'client_id',
//            'status_id',
//            'cancellation_reason_id',
            'date',
            'trip_transport_id',
//            'confirm_selected_transport',
//            'fact_trip_transport_id',
            'direction_id',

            //'street_id_from',
            //'point_id_from',
            'yandex_point_from_id',
            'yandex_point_from_name',
            'yandex_point_from_lat',
            'yandex_point_from_long',
            'time_air_train_arrival',
            //'street_id_to',
            //'point_id_to',
            'yandex_point_to_id',
            'yandex_point_to_name',
            'yandex_point_to_lat',
            'yandex_point_to_long',

            'time_air_train_departure',
            'prize_trip_count',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'time_confirm',
            'time_confirm_auto',
            'time_confirm_sort',
            //'time_vpz',
            'is_confirmed',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_sat',
            'use_fix_price',
//            'source_price',
            'fix_price',
            'price',
            'paid_summ',
            'paid_time',
            'accrual_cash_back',
            'is_paid',
//            'first_writedown_click_time',
//            'first_writedown_clicker_id',
//            'first_confirm_click_time',
//            'first_confirm_clicker_id',
            'confirm_click_time',
            'confirm_clicker_id',
//            'last_writedown_click_time',
//            'last_writedown_clicker_id',
//            'last_confirm_click_time',
//            'last_confirm_clicker_id',

            'created_at',
            'updated_at',
            'radio_confirm_now',
            'radio_group_1',
            'radio_group_2',
            'radio_group_3',
            'relation_order_id',
        ];

        // сценарий обновления заказа при нажатии на кнопку "Записать"
        $scenarios['writedown_button_update'] = [
            //'client_ext_id',
            //'client_server_ext_id',
            'external_id',
            'external_type',
            'external_created_at',
            'date',
            'client_id',
            'status_id',
            'status_setting_time',
//            'cancellation_reason_id',
            //'date',
            'trip_transport_id',
//            'confirm_selected_transport',
//            'fact_trip_transport_id',
            'direction_id',
            //'street_id_from',
            //'point_id_from',
            'yandex_point_from_id',
            'yandex_point_from_name',
            'yandex_point_from_lat',
            'yandex_point_from_long',

            'time_air_train_arrival',
            //'street_id_to',
            //'point_id_to',
            'yandex_point_to_id',
            'yandex_point_to_name',
            'yandex_point_to_lat',
            'yandex_point_to_long',

            'time_air_train_departure',
            'prize_trip_count',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'time_confirm',
            'time_confirm_auto',
            'time_confirm_sort',
            // 'time_vpz',
            'is_confirmed',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_sat',
            'use_fix_price',
            'fix_price',
            'price',
            'paid_summ',
            'paid_time',
            'accrual_cash_back',
            'is_paid',
//            'first_writedown_click_time',
//            'first_writedown_clicker_id',

//            'first_confirm_click_time',
//            'first_confirm_clicker_id',
            'confirm_click_time',
            'confirm_clicker_id',
            'last_writedown_click_time',
            'last_writedown_clicker_id',
            'last_confirm_click_time',
            'last_confirm_clicker_id',
            'created_at',
            'updated_at',
            'radio_confirm_now',
            'radio_group_1',
            'radio_group_2',
            'radio_group_3',
            'forced',
            'relation_order_id',
            'push_send_time',
        ];

        // сценарий получений полей для проверки времени подтверждения заказа
//        $scenarios['confirm_button_click'] = [
//            'date',
//            'trip_id',
//            'time_confirm',
//            'is_confirmed',
//        ];

        // сценарий получения полей для рассчета цены заказа
        $scenarios['calculate_price'] = [
            'date',
            //'street_id_from',
            //'point_id_from',
            'yandex_point_from_id',
            'yandex_point_from_name',
            'yandex_point_from_lat',
            'yandex_point_from_long',
            //'street_id_to',
            //'point_id_to',
            'yandex_point_to_id',
            'yandex_point_to_name',
            'yandex_point_to_lat',
            'yandex_point_to_long',

            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'use_fix_price',
            'fix_price',
            'price',
            'paid_summ',
            'paid_time',
            'is_paid',
            // пока не используются: 'accrual_cash_back', 'penalty_cash_back', 'cash_received_time', 'used_cash_back',
            'trip_id',
            'informer_office_id',
        ];

        // сценарий проверки полей формы выше желтой полосы "Место вам точно есть."
        $scenarios['check_form_fields'] = [
            'id',
            'client_id',
//            'status_id',
//            'cancellation_reason_id',
            'date',
            'trip_transport_id',
//            'confirm_selected_transport',
//            'fact_trip_transport_id',
            'direction_id',
            //'street_id_from',
            //'point_id_from',
            'yandex_point_from_id',
            'yandex_point_from_name',
            'yandex_point_from_lat',
            'yandex_point_from_long',
            'time_air_train_arrival',
            //'street_id_to',
            //'point_id_to',
            'yandex_point_to_id',
            'yandex_point_to_name',
            'yandex_point_to_lat',
            'yandex_point_to_long',

            'time_air_train_departure',
            'prize_trip_count',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_sat',
            'use_fix_price',
            'fix_price',
            'price',
            'paid_summ',
            'paid_time',
            'accrual_cash_back', // 'penalty_cash_back', 'used_cash_back',
            'is_paid',
            'relation_order_id',
        ];

        // сценарий отмены заказа
        $scenarios['cancel'] = [
            'cancellation_reason_id',
            'cancellation_click_time',
            'cancellation_clicker_id',
            'canceled_by',
            'accrual_cash_back',
            'penalty_cash_back', // 'used_cash_back',
            'cash_received_time',
            //'client_ext_id'
        ];

        $scenarios['penalty'] = [
            'has_penalty',
            'penalty_comment',
            'penalty_time',
            'penalty_author_id',
            'accrual_cash_back',
            'penalty_cash_back',
            'cash_received_time',
            'used_cash_back',
        ];

        $scenarios['pay_or_cancel_pay'] = [
            'paid_summ',
            'paid_time',
            'is_paid',
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //'is_mobile' => 'Заказ создан в приложении',
            //'client_ext_id' => 'Заявка',
            //'client_server_ext_id' => 'Заявка на клиенском сервере',
            'external_id' => 'id записи внешнего источника',
            'external_type' => 'Тип сущности внешнего источника',
            'external_created_at' => 'Время создания заявки на внешнем сервере',
            'sync_date' => 'Дата синхронизации с клиенским сервером',
            'status_id' => 'Статус',
            'status_setting_time' => 'Время установки статуса',
            //'date_sended' => 'Время отправки машины',
            'cancellation_reason_id' => 'Причина отмены',
            'cancellation_click_time' => 'Время отмены',
            'cancellation_clicker_id' => 'Пользователь совершивший отмену',
            'canceled_by' => 'Кем отменен заказ',
            'date' => 'Дата',
            'client_id' => 'Клиент',
            'client_name' => 'ФИО клиента',
            'confirm_selected_transport' => 'Клиент согласился с посадкой в выбранное т/с',
            'fact_trip_transport_id' => 'Фактическая машина',
            'fact_trip_transport_car_reg' => 'Гос. номер т/с',
            'direction_id' => 'Направление',

            'street_id_from' => 'Улица (откуда)',
            'street_id_to' => 'Улица (куда)',
            'point_id_from' => 'Откуда',
            'client_position_from_lat' => 'Желаемое место посадки клиента - широта',
            'client_position_from_long' => 'Желаемое место посадки клиента - долгота',
            'yandex_point_from_id' => 'Точка Откуда',
            'yandex_point_from_name' => 'Название яндекс-точки откуда',
            'yandex_point_from_lat' => 'Широта яндекс-точки откуда',
            'yandex_point_from_long' => 'Долгота яндекс-точки откуда',
            'point_id_to' => 'Куда',
            'yandex_point_to_id' => 'Точка Куда',
            'yandex_point_to_name' => 'Название яндекс-точки куда',
            'yandex_point_to_lat' => 'Широта яндекс-точки куда',
            'yandex_point_to_long' => 'Долгота яндекс-точки куда',

            'time_air_train_arrival' => 'Время прибытия поезда / посадки самолета',
            'time_air_train_departure' => 'Время отправления поезда / начало регистрации авиарейса',
            'trip_id' => 'Рейс',
            'informer_office_id' => 'Информаторская',

            'is_not_places' => 'Без места', // отправляется посылка - т.е. занимается нефизическое место
            'places_count' => 'Количество мест всего',
            'student_count' => 'Количество студенческих мест',
            'child_count' => 'Количество детских всего',
            'bag_count' => 'Количество сумок',
            'suitcase_count' => 'Количество чемоданов',
            'oversized_count' => 'Количество негабаритов',
            'prize_trip_count' => 'Количество призовых поездок', // расчитывается в коде
            //'baggage' => 'Багаж',
            'source_price' => 'Цена установленная в источнике',
            'use_fix_price' => 'Фиксированная цена',
            'time_confirm' => 'Время подтверждения',
            'time_confirm_auto' => 'Автоматическое время подтверждения',
            'time_confirm_sort' => 'Поле сортировки времени подтвеждения',
            // 'time_vpz' => 'ВПЗ', // Время первичной записи - редактируемое поле которое определяет приоритет внимания к заказу на рейсе
            'is_confirmed' => 'Подтвержден',
            'comment' => 'Пожелания',
            'additional_phone_1' => 'Дополнительный телефон 1',
            'additional_phone_2' => 'Дополнительный телефон 2',
            'additional_phone_3' => 'Дополнительный телефон 3',
            'time_sat' => 'Время посадки в машину', // когда диспетчер на странице "информация о рейсе" щелкает на кнопку посадки
            'time_satter_user_id' => 'Пользователь нажавший кнопку "Посадить"',
            'confirmed_time_sat' => 'Время подтверждения посадки',
            'confirmed_time_satter_user_id' => 'Пользователь нажавший кнопку "Подтвердить посадку"',
            'price' => 'Цена',
            'paid_summ' => 'Оплачено',
            'litebox_uuid' => 'uuid операции, возвращенный сервером',
            'litebox_fn_number' => 'ФН номер (номер фискального накопителя)',
            'litebox_fiscal_document_number' => 'Фискальный номер документа',
            //'litebox_fiscal_receipt_number' => 'Номер чека в смене',
            'litebox_fiscal_document_attribute' => 'Фискальный признак документа',

            'paid_time' => 'Время оплаты', // по факту время синхронизации когда пришли данные по оплате
            'accrual_cash_back' => 'Начисление кэш-бэка',
            'penalty_cash_back' => 'Использованный кэш-бэк для оплаты заказа',
            'cash_received_time' => 'Деньги за заказ получены',
            'used_cash_back' => 'Списанный кэш-бэк как штраф',
            'is_paid' => 'Заказ полностью оплачен - да/нет',
            'fix_price' => 'Фикс. цена',
            'first_opened_form_time' => 'Время первого открытия формы заказа',
            'first_opened_form_user_id' => 'Пользователь первым открывший форму заказа',
            'first_writedown_click_time' => "Время первичного нажатия кнопки Записать",
            'first_writedown_clicker_id' => "Пользователь (диспетчер) впервые нажавший кнопку Записать",
            'first_confirm_click_time' => "Время первичного нажатия кнопки Подтвердить",
            'first_confirm_clicker_id' => "Пользователь (диспетчер) впервые нажавший кнопку Подтвердить",

            'last_writedown_click_time' => "Время последнего нажатия кнопки Записать",
            'last_writedown_clicker_id' => "Пользователь (диспетчер) последним нажавший кнопку Записать",
            'last_confirm_click_time' => "Время последнего нажатия кнопки Подтвердить",
            'last_confirm_clicker_id' => "Пользователь (диспетчер) последним нажавший кнопку Подтвердить",

            'has_penalty' => 'Оштрафован',
            'penalty_comment' => 'Комментарий к штафу',
            'penalty_time' => 'Дата-время штрафования',
            'penalty_author_id' => 'Пользователь оштрафовавший клиента',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',

            'radio_confirm_now' => 'Группа переключателей "Подтвердить сейчас" / "Не подтверждать"',
            'radio_group_1' => 'Группа переключателей для выбора времени готовности встречи с машиной',
            'radio_group_2' => 'Группа переключателей для выбора времени контрольного звонка',
            'radio_group_3' => 'Группа переключателей завершающих заказ',
            'push_send_time' => 'Время отправки пуша',
            'push_confirm_time' => 'Время подтверждения пуша',
            'push_rejection_time' => 'Время отказа пуша',
            'relation_order_id' => 'Связанный заказ'
        ];
    }


    public function beforeValidate()
    {
        if(!empty($this->places_count)) {
            $this->places_count = intval($this->places_count);
        }
        if(!empty($this->student_count)) {
            $this->student_count = intval($this->student_count);
        }
        if(!empty($this->child_count)) {
            $this->child_count = intval($this->child_count);
        }
        if(!empty($this->bag_count)) {
            $this->bag_count = intval($this->bag_count);
        }
        if(!empty($this->suitcase_count)) {
            $this->suitcase_count = intval($this->suitcase_count);
        }
        if(!empty($this->oversized_count)) {
            $this->oversized_count = intval($this->oversized_count);
        }

        if(isset($this->date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->date = strtotime($this->date);   // convent '07.11.2016' to unixtime
        }

        if(in_array($this->scenario, ['writedown_button_create', 'writedown_button_update', 'calculate_price', 'check_form_fields'])) {

            if (isset($this->use_fix_price) && ($this->use_fix_price === 'true')) {
                $this->use_fix_price = true;
            } elseif (isset($this->use_fix_price) && ($this->use_fix_price === 'false')) {
                $this->use_fix_price = false;
            }

            if ($this->use_fix_price == true) {
                $this->fix_price = str_replace(' ', '', $this->fix_price);
                // устаняю косяк обработки данных в js картика элементом MaskMoney
                if (strpos($this->fix_price, '.') !== false) {
                    $this->fix_price = 1000 * $this->fix_price;
                }

                $this->price = $this->fix_price;

            } else {
                // рассчитаем цену на основе полученных данных
//                $this->prize_trip_count = $this->prizeTripCount;

                //exit('prize_trip_count='.$this->prize_trip_count);

//                if(empty($this->client_ext_id)) {
//                    $this->price = $this->calculatePrice;
//                }
            }
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            // $this->time_vpz = time();  - это копия поля first_writedown_click_time

            if(($this->status_id === null || $this->status_id === 0) && !empty($this->client_id)) {
                if(!empty($this->temp_identifier)) { // если заказ был создан из формы, то он получает не нулевой статус
                    $order_status = OrderStatus::getByCode('created');
                    $this->status_id = $order_status->id;
                    $this->status_setting_time = time();

                    //$this->first_writedown_click_time = time();
                    //$this->first_writedown_clicker_id = Yii::$app->user->id;

                    //DispatcherAccounting::createLog('order_create', $this->id);// логируем Первичную запись
                }
            }

            // здесь создается заявко-заказ с нулевым статусом автоматом
//            if($this->client_server_ext_id > 0 && ($this->status_id === null || $this->status_id === 0)) {
//                IncomingOrdersWidget::updateIncomingClientextOrders();
//            }

        }else {

            $this->updated_at = time();
            $this->sync_date = null;

            if(($this->status_id === null || $this->status_id === 0) && !empty($this->client_id)) {
                $order_status = OrderStatus::getByCode('created');
                $this->status_id = $order_status->id;
                $this->status_setting_time = time();

                //$this->first_writedown_click_time = time();
                //$this->first_writedown_clicker_id = Yii::$app->user->id;

                //DispatcherAccounting::createLog('order_create', $this->id);// логируем Первичную запись

                // обновляем окно заявко-заказов (если заявко-заказ получает статус)
//                if($this->external_type == 'client_server_client_ext') {
//                    IncomingOrdersWidget::updateIncomingClientextOrders();
//                }
            }else {
                //DispatcherAccounting::createLog('order_update', $this->id);// логируем Редактирование заказа
            }
        }

        $this->prize_trip_count = $this->prizeTripCount;



        if(!empty($this->confirm_click_time)) {

            if(empty($this->first_confirm_click_time)) {
                $this->first_confirm_click_time = $this->confirm_click_time;
            }else {
                $this->last_confirm_click_time = $this->confirm_click_time;
            }
        }
        if(!empty($this->confirm_clicker_id)) {
            if(empty($this->first_confirm_clicker_id)) {
                $this->first_confirm_clicker_id = $this->confirm_clicker_id;
            }else {
                $this->last_confirm_clicker_id = $this->confirm_clicker_id;
            }
        }

        // is_not_places    places_count

        if(preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_confirm)) {
            $hour_minute = explode(':', $this->time_confirm);
            $this->time_confirm = $this->date + intval($hour_minute[0])*3600 + intval($hour_minute[1])*60;
        }
        $this->is_confirmed = !empty($this->time_confirm);

        if(!empty($this->time_confirm)) {
            $this->time_confirm_sort = 1000*intval(date('H', $this->time_confirm)) + 10*intval(date('i', $this->time_confirm));
        }

        if(isset($this->time_air_train_departure) && preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_air_train_departure)) {
            $hour_minute = explode(':', $this->time_air_train_departure);
            $this->time_air_train_departure = trim($hour_minute[0]).':'.trim($hour_minute[1]);
        }
        if(isset($this->time_air_train_arrival) && preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_air_train_arrival)) {
            $hour_minute = explode(':', $this->time_air_train_arrival);
            $this->time_air_train_arrival = trim($hour_minute[0]).':'.trim($hour_minute[1]);
        }

        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/i', $this->time_sat)) {
            $this->time_sat = strtotime($this->time_sat);   // convent '07.11.2016 01:25' to unixtime
        }


        $this->price = $this->calculatePrice;
//        if(!($this->external_type == "client_site" && $this->is_paid == true)) {
//            $this->accrual_cash_back = $this->getCalculateAccrualCashBack($this->price);
//        }


        // установим редактируемому заказу наименьшее ВПЗ на данном рейсе
        if(isset($this->forced) && $this->forced == 'true') {
            $min_time_vpz = 0;
            $trip_orders = Order::find()
                ->where(['trip_id' => $this->trip_id])
                ->andWhere(['!=', 'id', $this->id])
                ->all();
            foreach($trip_orders as $order) {
                if($min_time_vpz == 0) {
                    $min_time_vpz = $order->first_writedown_click_time;
                }elseif($order->first_writedown_click_time < $min_time_vpz) {
                    $min_time_vpz = $order->first_writedown_click_time;
                }
            }

            if($min_time_vpz < $this->first_writedown_click_time) {
                $this->first_writedown_click_time = $min_time_vpz - 60; // ставим на 1 минуту раньше самого минимального времени
            }
            $this->confirm_selected_transport = false;
            $this->fact_trip_transport_id = 0;
        }


        if(in_array($this->scenario, ['writedown_button_update', 'check_form_fields'])) {
            // если пришли все переключатели кроме нижнего пустыми, значит значения этих переключателей не менялись
            if(empty($this->radio_confirm_now) && empty($this->radio_group_1) && empty($this->radio_group_2)) {

                if(isset($this->oldAttributes['radio_confirm_now'])) {
                    $this->radio_confirm_now = $this->oldAttributes['radio_confirm_now'];
                }
                if(isset($this->oldAttributes['radio_group_1'])) {
                    $this->radio_group_1 = $this->oldAttributes['radio_group_1'];
                }
                if(isset($this->oldAttributes['radio_group_2'])) {
                    $this->radio_group_2 = $this->oldAttributes['radio_group_2'];
                }

            }else { // иначе сбрасываем старые значения
                $this->confirm_selected_transport = false;
                $this->fact_trip_transport_id = NULL;
                $this->fact_trip_transport_car_reg = NULL;
                $this->time_sat = null;
            }
        }

        if(!empty($this->radio_group_1) && !empty($this->trip_transport_id)) {

            // $trip_transport = TripTransport::find()->where(['id' => $this->trip_transport_id, 'status_id' => 0, 'date_sended' => NULL])->one();
            $trip_transport = TripTransport::find()->where(['id' => $this->trip_transport_id, ])->one();
            if($trip_transport == null) {
                throw new ForbiddenHttpException('Выбранное транспотрное средство не найдено');
            }

            $this->confirm_selected_transport = true;
            $this->fact_trip_transport_id = $this->trip_transport_id;
        }


        if($this->factTripTransport != null && $this->factTripTransport->transport != null) {
            $this->fact_trip_transport_car_reg = $this->factTripTransport->transport->car_reg;
        }else {
            $this->fact_trip_transport_car_reg = '';
        }

        if($this->client != null) {
            $this->client_name = $this->client->name;
        }else {
            $this->client_name = '';
        }

//        if (!$this->isNewRecord) {
//            $this->setClientExtStatus();
//        }

        // отмена статуса "отменен"
        if(isset($this->oldAttributes['status_id']) && $this->oldAttributes['status_id'] == 2 && $this->oldAttributes['status_id'] != $this->status_id) {
            $this->cancellation_reason_id = NULL;
        }


        // сообщим браузерам что надо обновить страницы: главную, состав рейса и расстановка
        // если изменилась рейс - обновляем, если изменилась дата - значит и рейс изменился.
        if(isset($this->oldAttributes['trip_id']) && $this->oldAttributes['trip_id'] > 0
            && $this->oldAttributes['trip_id'] != $this->trip_id && $this->status_id > 0)
        {
            SocketDemon::updateMainPages($this->oldAttributes['trip_id'], $this->oldAttributes['date']);
        }
        if($this->trip_id > 0 && $this->status_id > 0) {
            SocketDemon::updateMainPages($this->trip_id, $this->date);
        }

        return parent::beforeSave($insert);
    }


    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->relation_order_id > 0) {
            $relation_order = Order::findOne($this->relation_order_id);
            $relation_order->setField('relation_order_id', $this->id);
        }

        if ($insert == true) {

            // для логов в dispatcher_accounting где есть такой идентификатор проставляем order_id
            if(!empty($this->temp_identifier)) {
                $dispatcher_accountings =
                    DispatcherAccounting::find()->where(['order_temp_identifier' => $this->temp_identifier])->all();

                if(count($dispatcher_accountings) > 0) {
                    DispatcherAccounting::setFields(ArrayHelper::map($dispatcher_accountings, 'id', 'id'), 'order_id', $this->id);
                }
            }
        }

        $order_passengers = OrderPassenger::find()->where(['order_id' => $this->id])->all();
        if(count($order_passengers) > $this->places_count) { // удалим связки с лишними пассажирами
            for($i = $this->places_count; $i < count($order_passengers); $i++) {
                $order_passengers[$i]->delete();
            }
        }


        // обновляем окно "Активных рейсов" и красный-желтый у названий рейсов
        $trip = $this->trip;
        $old_trip = null;
        if(isset($changedAttributes['trip_id']) && $changedAttributes['trip_id'] != $this->trip_id) {
            $old_trip = Trip::find()->where(['id' => $changedAttributes['trip_id']])->one();
        }
        if(
            ($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended))
            || ($old_trip != null && !empty($old_trip->date_start_sending) && empty($old_trip->date_sended))
        ) {
            IncomingOrdersWidget::updateActiveTripsModal();
        }

        if($this->external_id > 0 && $this->external_type == 'client_site' && isset($changedAttributes['status_id']) ) {

            if($changedAttributes['status_id'] != $this->status_id && empty($changedAttributes['status_id'])) {

                // если заявко-заказ перешел из нулевого статуса в другой, то обновляем окно заявко-заказов
                IncomingOrdersWidget::updateIncomingClientextOrders();
            }
        }
        if($this->external_id > 0 && $this->external_type == 'client_site' && isset($changedAttributes['status_id']) ) {

            if($changedAttributes['status_id'] != $this->status_id && empty($changedAttributes['status_id'])) {

                // если заказ созданный из заявки перешел из нулевого статуса в другой, то обновляем окно Эл-х заявок
                IncomingOrdersWidget::updateIncomingRequestOrders();
            }
        }

//        elseif($this->client_server_ext_id > 0 && $changedAttributes['first_opened_form_time']) {
//
//            // если была открыта форма для редактирования заявко-заказа (то в окне заяво-заказов подсвечивается текущий заявко-заказ)
//            IncomingOrdersWidget::updateIncomingClientextOrders();
//        }
    }


    // не вижу смысла обновлять данные заявок на основном сервере
//    public function setClientExtStatus() {
//
//        if(!empty($this->client_ext_id)) {
//
//            $client_ext = $this->clientExt;
//
//            $aOrderStatuses = ArrayHelper::map(OrderStatus::find()->all(), 'id', 'code');
//
//            if($this->status_id > 0 && $aOrderStatuses[$this->status_id] == 'created') {
//                if (empty($this->time_confirm)) {
//                    $client_ext->status = 'pending_call';
//                } else {
//                    $client_ext->status = 'pending_send';
//                }
//            }elseif($aOrderStatuses[$this->status_id] == 'canceled') {
//                $client_ext->status = 'canceled';
//            }elseif($aOrderStatuses[$this->status_id] == 'sent') {
//                $client_ext->status = 'sended';
//            }else {
//                $client_ext->status = 'unknown';
//            }
//
//            if(!$client_ext->save(false)) {
//                throw new ErrorException('Не удалось обновить статус заявки');
//            }
//        }
//
//        return true;
//    }

    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_name == 'fact_trip_transport_id') {
            $trip_transport = TripTransport::find()->where(['id' => $field_value])->one();
            if($trip_transport != null && $trip_transport->transport != null) {
                $this->setField('fact_trip_transport_car_reg', $trip_transport->transport->car_reg);
            }else {
                $this->setField('fact_trip_transport_car_reg', '');
            }
        }elseif($field_name == 'client_id') {
            $client = Client::find()->where(['id' => $field_value])->one();
            if($client != null) {
                $this->setField('client_name', $client->name);
            }else {
                $this->setField('client_name', '');
            }
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }

        $res = Yii::$app->db->createCommand($sql)->execute();

        if($this->trip_id > 0) { // сообщим браузерам что надо обновить страницу рейсов
            SocketDemon::updateMainPages($this->trip_id, $this->date);
        }

        // обновляем окно "Активных рейсов" и красный-желтый у названий рейсов
        $trip = $this->trip;
        if($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
            IncomingOrdersWidget::updateActiveTripsModal();
        }

        return $res;
    }

    public function getRadioConfirmNow() {
        return [
            1 => 'Подтвердить сейчас',
            2 => 'Не подтверждать'
        ];
    }

    public function getClearRadioGroup1() {
        $list = [
            //1 => 'Будьте собраны и готовы в {ВРПТ}, без звонка не выходите',
            //2 => 'Вам нужно быть на {ТЧК_ОТКУДА} в {ВРПТ}, подъедет машина номер ___'
//            1 => '<b><span class="npr">{НПР}</span>:</b> подъедет машина {ТС}, будьте собраны и готовы в <span class="vrpt">{ВРПТ}</span>, без звонка не выходите',
//            2 => '<b><span class="npr">{НПР}</span>:</b> вам нужно быть на {ТЧК_ОТКУДА} в <span class="vrpt">{ВРПТ}</span>, подъедет ___'

            1 => '<b>АК:</b> подъедет машина {ТС}, будьте собраны и готовы в <span class="vrpt">{ВРПТ}</span>, без звонка не выходите',
            2 => '<b>КА:</b> вам нужно быть на {ТЧК_ОТКУДА} в <span class="vrpt">{ВРПТ}</span>, подъедет ___'
        ];

        // если на рейс уже поставлены машины и они не уехали, то показываем список машин для выбора
        if(!empty($this->trip_id)) {

            // $trip_transports = TripTransport::find()->where(['trip_id' => $this->trip_id, 'status_id' => 0])->all();
            $trip_transports = TripTransport::find()->where(['trip_id' => $this->trip_id])->all();
            if(count($trip_transports) > 0) {
                $select_html = '<select name="Order[trip_transport_id]" disabled="true">';
                foreach($trip_transports as $trip_transport) {
                    if($trip_transport->used_places_count < $trip_transport->total_places_count) {
                        $select_html .= '<option value="' . $trip_transport->id . '">' . $trip_transport->transport->name4 . '</option>';
                    }
                }
                $select_html .= '</select>';
                $list[2] = str_replace('___', $select_html, $list[2]);

                $list[1] = str_replace('{ТС}', $select_html, $list[1]);
            }
        }

        return $list;
    }

    public function getRadioGroup1() {
        $list = [
            //1 => 'Будьте собраны и готовы в {ВРПТ}, без звонка не выходите',
            //2 => 'Вам нужно быть на {ТЧК_ОТКУДА} в <span class="vrpt">{ВРПТ}</span>, подъедет машина номер ___'
//            1 => '<b><span class="npr">{НПР}</span>:</b> подъедет машина {ТС}, будьте собраны и готовы в <span class="vrpt">{ВРПТ}</span>, без звонка не выходите',
//            2 => '<b><span class="npr">{НПР}</span>:</b> вам нужно быть на {ТЧК_ОТКУДА} в <span class="vrpt">{ВРПТ}</span>, подъедет ___'
            1 => '<b>АК:</b> подъедет машина {ТС}, будьте собраны и готовы в <span class="vrpt">{ВРПТ}</span>, без звонка не выходите',
            2 => '<b>КА:</b> вам нужно быть на {ТЧК_ОТКУДА} в <span class="vrpt">{ВРПТ}</span>, подъедет ___'
        ];

//        if(!empty($this->direction_id)) {
//            $direction = $this->direction;
//            $list[1] = str_replace('{НПР}', $direction->sh_name, $list[1]);
//            $list[2] = str_replace('{НПР}', $direction->sh_name, $list[2]);
//        }

        if(!empty($this->time_confirm)) {

            if(preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_confirm)) {
                $hour_minute = explode(':', $this->time_confirm);

                if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                    $this->date = strtotime($this->date);
                }

                $this->time_confirm = $this->date + intval($hour_minute[0])*3600 + intval($hour_minute[1])*60;
            }

            $list[1] = str_replace('{ВРПТ}', date('H:i', $this->time_confirm), $list[1]);
            $list[2] = str_replace('{ВРПТ}', date('H:i', $this->time_confirm), $list[2]);
        }

        if(!empty($this->yandex_point_from_name)) {
            $list[2] = str_replace('{ТЧК_ОТКУДА}', '&laquo;' . $this->yandex_point_from_name . '&raquo;', $list[2]);
        }

        // если на рейс уже поставлены машины и они не уехали, то показываем список машин для выбора
        if(!empty($this->trip_id)) {
            // $trip_transports = TripTransport::find()->where(['trip_id' => $this->trip_id, 'status_id' => 0])->all();
            $trip_transports = TripTransport::find()->where(['trip_id' => $this->trip_id])->all();
            if(count($trip_transports) > 0) {
                $select_html = '<select name="Order[trip_transport_id]" disabled="true">';
                foreach($trip_transports as $trip_transport) {
                    if($this->fact_trip_transport_id == $trip_transport->id) {
                        $select_html .= '<option selected="true" value="' . $trip_transport->id . '">' . $trip_transport->transport->name4 . '</option>';
                    }else {
                        $select_html .= '<option value="' . $trip_transport->id . '">' . $trip_transport->transport->name4 . '</option>';
                    }
                }
                $select_html .= '</select>';
                $list[2] = str_replace('___', $select_html, $list[2]);

                $list[1] = str_replace('{ТС}', $select_html, $list[1]);
            }
        }

        return $list;
    }

    public function getRadioGroup2() {// $day_code
        $list = [
            1 => 'Мы позвоним вам {ДАТА1} до 10:00 и скажем точное время и машину',
            2 => '{ДАТА2} вечером мы вам позвоним и скажем точное время и машину'
        ];

        if(!empty($this->date)) {
            if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                $this->date = strtotime($this->date);
            }
            $date1 = $this->date;
            if(date('d.m.Y', $date1) == date('d.m.Y')) {
                $date1 = 'сегодня';
            }elseif(date('d.m.Y', $date1) == date('d.m.Y', time() - 86400)) {
                $date1 = 'вчера';
            }elseif(date('d.m.Y', $date1) == date('d.m.Y', time() + 86400)) {
                $date1 = 'завтра';
            }else {
                $date1 = date('d.m.Y', $date1);
            }
            $list[1] = str_replace('{ДАТА1}', $date1, $list[1]);

            $date2 = $this->date - 86400;
            if(date('d.m.Y', $date2) == date('d.m.Y')) {
                $date2 = 'Сегодня';
            }elseif(date('d.m.Y', $date2) == date('d.m.Y', time() - 86400)) {
                $date2 = 'Вчера';
            }elseif(date('d.m.Y', $date2) == date('d.m.Y', time() + 86400)) {
                $date2 = 'Завтра';
            }else {
                $date2 = date('d.m.Y', $date2);
            }
            $list[2] = str_replace('{ДАТА2}', $date2, $list[2]);
        }

        return $list;
    }

    public function getRadioGroup3() {
        return [
            1 => 'Когда поедете обратно? Давайте вас запишем.',
            2 => 'Спасибо за заказ. До свидания.',
            3 => '+1 адрес к заказу'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreetFrom()
    {
        return $this->hasOne(Street::className(), ['id' => 'street_id_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreetTo()
    {
        return $this->hasOne(Street::className(), ['id' => 'street_id_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointFrom()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointTo()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYandexPointFrom()
    {
        return $this->hasOne(YandexPoint::className(), ['id' => 'yandex_point_from_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYandexPointTo()
    {
        return $this->hasOne(YandexPoint::className(), ['id' => 'yandex_point_to_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'trip_id']);
    }

    public function getCancellationReason() {
        return $this->hasOne(OrderCancellationReason::className(), ['id' => 'cancellation_reason_id']);
    }

    public function getFactTripTransport()
    {
        return $this->hasOne(TripTransport::className(), ['id' => 'fact_trip_transport_id']);
    }

    public function getInformerOffice()
    {
        return $this->hasOne(InformerOffice::className(), ['id' => 'informer_office_id']);
    }

    public function getCancellationClicker() {
        return $this->hasOne(User::className(), ['id' => 'cancellation_clicker_id']);
    }

    public function getFirstWritedownClicker() {
        return $this->hasOne(User::className(), ['id' => 'first_writedown_clicker_id']);
    }
    public function getFirstConfirmClicker() {
        return $this->hasOne(User::className(), ['id' => 'first_confirm_clicker_id']);
    }
    public function getLastWritedownClicker() {
        return $this->hasOne(User::className(), ['id' => 'last_writedown_clicker_id']);
    }
    public function getLastConfirmClicker() {
        return $this->hasOne(User::className(), ['id' => 'last_confirm_clicker_id']);
    }

//    public function getClientExt() {
//        return $this->hasOne(ClientExt::className(), ['id' => 'client_ext_id']);
//    }


    public function getPrizeTripCount()
    {
//        if($this->use_fix_price == true) {
//            return 0;
//        }

        if($this->trip != null && $this->trip->commercial == 1) {
            return 0;
        }

        if($this->informerOffice != null && $this->informerOffice->cashless_payment == 1) {
            return 0;
        }

        $client = $this->client;

        if($client == null) {
            $sended_orders_places_count = 0;
            $sended_prize_trip_count = 0;
            $penalty = 0;
        }else {

            //$sended_orders_places_count = $client->sended_orders_places_count;
            //$sended_orders_places_count = $client->past_years_sended_places + $client->current_year_sended_places;
            $sended_orders_places_count = $client->current_year_sended_places;

            //$sended_prize_trip_count = $client->sended_prize_trip_count;
            //$sended_prize_trip_count = $client->past_years_sended_prize_places + $client->current_year_sended_prize_places;
            $sended_prize_trip_count = $client->current_year_sended_prize_places;
            //$penalty = $client->penalty;
            //$penalty = $client->past_years_penalty + $client->current_year_penalty;
            $penalty = $client->current_year_penalty;

            // к количеству отправленных призовых поездок добавим поездки из "новых" заказов (но количество отправленных заказов оставим без изменения)
            $created_order_status = OrderStatus::getByCode('created');
            $created_orders_query = Order::find()
                ->where([
                    'client_id' => $client->id,
                    'status_id' => $created_order_status->id
                ]);
            if($this->id > 0) {
                $created_orders_query = $created_orders_query->andWhere(['!=', 'id', $this->id]);
            }
            $created_orders = $created_orders_query->all();
            // echo "created_orders:<pre>"; print_r($created_orders); echo "</pre>";

            if(count($created_orders) > 0) {
                foreach($created_orders as $order) {
                    if($order->external_type == "client_site" && $order->is_paid == true) {
                        continue;
                    }else {
                        $sended_prize_trip_count = $sended_prize_trip_count + $order->prize_trip_count;
                    }
                }
            }
        }


        $P = intval($this->places_count); // количество мест в текущем заказе

        if($this->is_not_places == 1)  // если отправляется посылка, то призовой поездки не предоставляется
            return 0;
        else {
            
            // echo "<br />sended_orders_places_count=$sended_orders_places_count sended_prize_trip_count=$sended_prize_trip_count penalty=$penalty<br />";
            
            if($P < 5) {
                $prize_count = floor(($sended_orders_places_count - 5*($sended_prize_trip_count + $penalty) + $P)/5);
                if($prize_count > 1) {
                    $prize_count = 1;
                }
                if($prize_count < 0) { // защита от случает "страшных" данных в базе
                    $prize_count = 0;
                }
            }else {
                $prize_count = floor($P/5); // считаем призовые только на основе текущего заказа без привязки к прошлым поездкам
            }


            return $prize_count;
        }
    }


    public function getCalculatePrice()
    {
        // определяем источник, по нему определяем признак формирования цены
        $do_tariff = null;
        if($this->client_id > 0) {
            $do_tariff = $this->client->doTariff;
        }
        if($do_tariff == null) {
            $informer_office = $this->informerOffice;
            if ($informer_office != null) {
                $do_tariff = $informer_office->doTariff;
            }
        }


        if($this->use_fix_price == true && $do_tariff == null ) {
            return $this->price;
        }


        if($do_tariff != null && $do_tariff->use_fix_price == true) {
            //$this->setField('use_fix_price', true);
            $this->use_fix_price = true;
        }else {
            //$this->setField('use_fix_price', false);
            $this->use_fix_price = false;
        }


        $COST = 0;
        if($this->use_fix_price == 1) {

            $COST = $this->price;

        }else {


            $P = intval($this->places_count); // количество мест в текущем заказе
            $S = intval($this->student_count); // количество студентов в текущем заказе
            $B = intval($this->child_count); // количество детей в текущем заказе

            $prize_count = $this->prizeTripCount; // количество призовых поездок в текущем заказе
            // echo "prize_count=$prize_count <br />";

            if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                $this->date = strtotime($this->date);
            }

            $trip = $this->trip;
            if ($trip == null) {
                //throw new ForbiddenHttpException('Рейс не найден');
                return 0;
            }
            $tariff = $trip->tariff;
            if ($tariff == null) {
                //throw new ForbiddenHttpException('Тариф не найден');
                return 0;
            }

            $T_RESERV = $tariff->unprepayment_reservation_cost; // стоимость бронирования

            $T_COMMON = $tariff->unprepayment_common_price + $T_RESERV;  // цена по общему тарифу
            $T_STUDENT = $tariff->unprepayment_student_price + $T_RESERV; // студенческий тариф
            $T_BABY = $tariff->unprepayment_baby_price + $T_RESERV;    // детский тариф
            $T_AERO = $tariff->unprepayment_aero_price + $T_RESERV;    // тариф аэропорт
            $T_LOYAL = $tariff->unprepayment_loyal_price + $T_RESERV;   // тариф призовой поездки
            $T_PARCEL = $tariff->unprepayment_parcel_price + $T_RESERV; // тариф отправки посылки (без места)


            // если клиенту едут в аэропорт, то они считаются по иной формуле
            $yandexPointTo = $this->yandexPointTo;
            $yandexPointFrom = $this->yandexPointFrom;

            if ($this->informerOffice != null && $this->informerOffice->cashless_payment == 1) {
                $COST = 10;
            } elseif ($this->is_not_places == 1) {
                //$COST = $T_PARCEL;
                $COST = ($do_tariff != null ? $do_tariff->changePlacePrice($T_PARCEL, $this) : $T_PARCEL);

            } elseif (
                ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
                || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
            ) { // едут в аэропорт или из аэропорта

                // здесь нужно сделать пересчет в соответствии с признаком цены
                if ($do_tariff != null) {
                    $COST = ($P - $prize_count) * $do_tariff->changePlacePrice($T_AERO, $this) + $prize_count * $do_tariff->changePlacePrice($T_LOYAL, $this);
                } else {
                    $COST = ($P - $prize_count) * $T_AERO + $prize_count * $T_LOYAL;
                }

            } else {


                //$COST = ($P - $prize_count - $S - $B)*$T_COMMON + $S*$T_STUDENT + $B*$T_BABY + $prize_count*$T_LOYAL;

                // Призовой тариф в заказе, где есть разные категории пассажиров (общий, дети, студенты), применяется к пассажиру с наименьшим тарифом

                // если $T_COMMON > $T_STUDENT и $T_COMMON > $T_BABY и $T_COMMON > $T_LOYAL
                //$COST = $P*$T_COMMON - $S*($T_COMMON - $T_STUDENT) - $B*($T_COMMON - $T_BABY)

                // т.е. по формуле логика должна быть такая:
                // беруться все типы мест помноженные на тарифы, из них вычитаюся кол-во призовых мест и складываются с призовыми местами умноженными на призовой тариф.
                // а должно быть

                // есть места обычные, студенческие, детсики. И призы отдельно считаются.
                // 0. чистая формула без призов:  $COST = ($P - $S - $B)*$T_COMMON + $S*$T_STUDENT + $B*$T_BABY
                // 1. составляется массив всех мест (общих, студенческих, детских), отсортированных по возрастанию цены за каждый.
                // 2. кол-во первых массив соответствующего кол-во призовых мест заменяется ценой $T_LOYAL
                // 3. суммируются цены за места и получается общая цена

                // составляется массив всех цен за места (общих, студенческих, детских)
                $aPlacesPrice = [];
                $P = $P - $S - $B;
                for ($i = 0; $i < $P; $i++) {
                    $aPlacesPrice[] = $T_COMMON;
                }
                for ($i = 0; $i < $S; $i++) {
                    $aPlacesPrice[] = $T_STUDENT;
                }
                for ($i = 0; $i < $B; $i++) {
                    $aPlacesPrice[] = $T_BABY;
                }
                sort($aPlacesPrice);

                // кол-во первых массив соответствующего кол-во призовых мест заменяется ценой $T_LOYAL
                for ($i = 0; $i < $prize_count; $i++) {
                    $aPlacesPrice[$i] = $T_LOYAL;
                }
                // echo "aPlacesPrice:<pre>"; print_r($aPlacesPrice); echo "</pre>";

                // суммируются цены за места и получается общая цена заказа
                foreach ($aPlacesPrice as $placePrise) {
                    //$COST += $placePrise;
                    if ($do_tariff != null) {
                        $COST += $do_tariff->changePlacePrice($placePrise, $this);
                    } else {
                        $COST += $placePrise;
                    }
                }
            }
        }

        if($do_tariff != null) {
            $COST = $do_tariff->changeTotalPrice($COST, $this);
        }


        return $COST;
    }

    public function getCalculateAccrualCashBack($price)
    {
        $trip = $this->trip;
        if ($trip == null) {
            return 0;
        }

        $cashback_setting = CashbackSetting::find()
			->where(['<=', 'start_date', $trip->date])
			//->andWhere(['with_commercial_trips' => intval($trip->commercial)])
			->orderBy(['start_date' => SORT_DESC])
			->one();
        if($cashback_setting == null) {
            return 0;
        }

        if($trip->commercial == true && $cashback_setting->with_commercial_trips == 0) {
            return 0; // для коммерческих рейсов не накапливается кэш-бэк
        }

        // может быть проблема что в заказе цена пересчиталась, внутри модели заказа осталась старая цена, поэтому
        // в функцию явно передается $price, а не береться из параметра $this->price
        if($this->is_paid == true) {
            if($cashback_setting->has_cashback_for_prepayment == true) {
                return $price*$cashback_setting->order_accrual_percent/100;
            }else {
                return 0;
            }
        }else {
            if($cashback_setting->has_cashback_for_nonprepayment == true) {
                return $price*$cashback_setting->order_accrual_percent/100;
            }else {
                return 0;
            }
        }
    }

    public function getCalculatePenaltyCashBack($price)
    {
        $trip = $this->trip;
        if ($trip == null) {
            return 0;
        }

        $cashback_setting = CashbackSetting::find()
            ->where(['<=', 'start_date', $trip->date])
            //->andWhere(['with_commercial_trips' => intval($trip->commercial)])
            ->orderBy(['start_date' => SORT_DESC])
            ->one();
        if($cashback_setting == null) {
            return 0;
        }

        // происходит ли отмена менее чем за hours_before_start_trip_for_penalty часов до начала рейса
        $hour_minute = explode(':', $trip->start_time);
        $start_trip = $trip->date + intval($hour_minute[0])*3600 + intval($hour_minute[1])*60;

        if(time() + 3600*$cashback_setting->hours_before_start_trip_for_penalty >= $start_trip) {
            // может быть проблема что в заказе цена пересчиталась, внутри модели заказа осталась старая цена, поэтому
            // в функцию явно передается $price, а не береться из параметра $this->price
            return $price*$cashback_setting->order_penalty_percent/100;
        }else {
            return 0;
        }
    }


    public function getPenaltyAuthor() {
        return $this->hasOne(User::className(), ['id' => 'penalty_author_id']);
    }


    /*
     * Все изменения статуса заказа нужно делать через этот метод!!!
     *
     * @param $code
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \yii\db\Exception
     */
    public function setStatus($code) {

        $order_status = OrderStatus::getByCode($code);

        if($code == 'canceled') { // отменяется заказ, увеличивается счетчик отмененных заказов

            // если текущее время отмены заказа больше чем время (первая точка рейча минут часы $setting->count_hours_before_trip_to_cancel_order),
            // то отмена заказа запрещена
            $setting = Setting::find()->where(['id' => 1])->one();
            $trip = $this->trip;
            if($trip != null && $this->paid_summ > 0) {
                if(time() > $trip->getStartTimeUnixtime() - 3600*intval($setting->count_hours_before_trip_to_cancel_order)) {
                    throw new ForbiddenHttpException('Запрещено отменять заказ менее чем за '.$setting->count_hours_before_trip_to_cancel_order.' часов до рейса');
                }
            }

            if(isset(Yii::$app->user)) {

                $current_user = User::findOne(Yii::$app->user->id);

                $sql = '
                    UPDATE `' . self::tableName() . '`
                    SET
                        status_id = ' . $order_status->id . ',
                        status_setting_time = ' . time() . ',
                        cancellation_reason_id = ' . $this->cancellation_reason_id . ',
                        cancellation_click_time = '.time().',
                        canceled_by = "operator",
                        cancellation_clicker_id = '.$current_user->id.',
                        time_sat = NULL,
                        time_satter_user_id = NULL,
                        confirmed_time_sat = NULL,
                        confirmed_time_satter_user_id = NULL,
                        confirm_selected_transport = 0,
                        fact_trip_transport_id = NULL,
                        fact_trip_transport_car_reg = NULL,
                        time_confirm = NULL,
                        time_confirm_sort = NULL,
                        is_confirmed = 0,
                        updated_at = '.time().'
                    WHERE id = ' . $this->id;

            }else {

                $sql = '
                    UPDATE `' . self::tableName() . '`
                    SET
                        status_id = ' . $order_status->id . ',
                        status_setting_time = ' . time() . ',

                        cancellation_click_time = '.time().',
                        canceled_by = "operator",
                        time_sat = NULL,
                        confirm_selected_transport = 0,
                        fact_trip_transport_id = NULL,
                        fact_trip_transport_car_reg = NULL,
                        time_confirm = NULL,
                        time_confirm_sort = NULL,
                        is_confirmed = 0,
                        updated_at = '.time().'
                    WHERE id = ' . $this->id;
            }


            Yii::$app->db->createCommand($sql)->execute();

            $client = $this->client;
            if($client != null) {

                // счетчики клиента нужно обновить
                $order = Order::find()->where(['id' => $this->id])->one();
                $client->recountSendedCanceledReliabilityCounts($order, 0, 0, 1, $order->places_count);
            }

            // отменяем оплату
            $order->cancelPay();


        }else {

            //$prev_status = $this->status;

            $sql = '
                UPDATE `' . self::tableName() . '`
                SET
                    status_id = ' . $order_status->id . ',
                    status_setting_time = ' . time() . ',
                    cancellation_reason_id = NULL,
                    cancellation_click_time = NULL,
                    canceled_by = "",
                    updated_at = '.time().'
                WHERE id = ' . $this->id;
            Yii::$app->db->createCommand($sql)->execute();
        }

        $order = Order::find()->where(['id' => $this->id])->one(); // получаем модель с обновленными данными

        // сообщим браузерам что надо обновить страницу рейсов
        if($this->trip_id > 0) {
            SocketDemon::updateMainPages($order->trip_id, $order->date);
        }

        // обновляем окно "Активных рейсов" и красный-желтый у названий рейсов
        $trip = $this->trip;
        if($trip != null && !empty($trip->date_start_sending) && empty($trip->date_sended)) {
            IncomingOrdersWidget::updateActiveTripsModal();
        }

        return true;
    }


    public function getYandexPointTimeConfirm() {

        $time_confirm = 0;

        // по данным: яндекс-точки отправки, последней точки рейса вычисляю врпт:
        $trip = $this->trip;
        if($trip == null) {
            //throw new ForbiddenHttpException('Рейс не найден');
            return $time_confirm;
        }

        $yandex_point_from = $this->yandexPointFrom;
        if($yandex_point_from == null) {
            //throw new ForbiddenHttpException('Точка отправки не найдена');
            return $time_confirm;
        }


        $aTripStart = explode(':', $trip->start_time);
        $trip_start_time_secs = 3600 * intval($aTripStart[0]) + 60 * intval($aTripStart[1]);
        $aTripEnd = explode(':', $trip->end_time);
        $trip_end_time_secs = 3600 * intval($aTripEnd[0]) + 60 * intval($aTripEnd[1]);


        $setting = Setting::find()->where(['id' => 1])->one();
        if($setting == null) {
            throw new ErrorException('Настройки не найдены');
        }
        if($trip->direction_id == 1) {
            //$max_time_short_trip = Trip::$max_time_short_trip_AK;
            $max_time_short_trip = $setting->max_time_short_trip_AK;
        }else {
            //$max_time_short_trip = Trip::$max_time_short_trip_KA;
            $max_time_short_trip = $setting->max_time_short_trip_KA;
        }

        if($trip_end_time_secs - $trip_start_time_secs <= $max_time_short_trip) { // короткий рейс

            if(intval($yandex_point_from->time_to_get_together_short) > 0) {
                $time_confirm = $trip_end_time_secs + $this->date - intval($yandex_point_from->time_to_get_together_short);
            }
        }else { // длинный рейс
            if(intval($yandex_point_from->time_to_get_together_long) > 0) {
                $time_confirm = $trip_end_time_secs + $this->date - intval($yandex_point_from->time_to_get_together_long);
            }
        }

        return $time_confirm;
    }


    function setPay($save_paid_data = true) {

        // $this->cash_received_time = time(); // здесь не нужно устанавливать
        if($save_paid_data) {

            if(!empty($this->paid_time)) {
                throw new ForbiddenHttpException('По заказу уже производился один платеж');
            }

            $this->scenario = 'pay_or_cancel_pay';
            $this->paid_summ = $this->price;
            $this->paid_time = time();
            $this->is_paid = true;
            if (!$this->save(false)) {
                throw new ForbiddenHttpException('Заказ не удалось сохранить');
            }
        }

        // запрос на создание чека
        LiteboxOperation::makeOperationSell($this); // вторая операция прихода по заказу запрещена!
    }

    function cancelPay() {

        // вызывается при отмене заказа, при высадке пассажира
        // заказ может быть не оплачен, частично оплачен, полностью оплачен
        // у заказа возможно уже были возвраты денег, или 2 операции оплаты и 1 возврат.

        // отменяем оплату
        //$this->cash_received_time = 0; // пусть этот флаг останеться не тронутым для истории

        if(empty($this->paid_time)) {
            //throw new ForbiddenHttpException('Нельзя отменить оплату, т.к. не было платежа');
            return false;
        }

        $this->scenario = 'pay_or_cancel_pay';
        $this->paid_summ = 0;
        $this->paid_time = 0;
        $this->is_paid = false;
        if(!$this->save(false)) {
            throw new ForbiddenHttpException('Заказ не удалось сохранить');
        }

        $litebox_operation = LiteboxOperation::find()->where(['order_id' => $this->id])->one();
        if($litebox_operation != null && empty($litebox_operation->sell_refund_at)) {
            $litebox_operation->makeOperationSellRefund();
        }

        return true;
    }

    /*
    public function sendToTrip() {

        $day_report_trip_transport = DayReportTripTransport::find()
            ->where(['trip_transport_id' => $this->fact_trip_transport_id])
            ->one();
        if($day_report_trip_transport == null) {
            throw new ForbiddenHttpException('Отчет дня связанный с т/с заказа - не найден');
        }

        $trip = $this->trip;
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        if($this->factTripTransport == null) {
            throw new ForbiddenHttpException('Машина привязанная к заказу не найдена');
        }

        $direction = $this->direction;
        if($direction == null) {
            throw new ForbiddenHttpException('Направление не найдено');
        }

        $transport = $this->factTripTransport->transport;
        if($transport == null) {
            throw new ForbiddenHttpException('Машина не найдена');
        }

        $driver = $this->factTripTransport->driver;
        if($driver == null) {
            throw new ForbiddenHttpException('Водитель');
        }

        // все заказы привязанные "фактически" к отправляемой машине перевожу в статус "Отправлен"
        // Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $order_status->id . ', status_setting_time=' . time() . ', updated_at=' . time() . ' WHERE id IN (' . implode(',', $aFactOrdersId) . ')')->execute();

        $order_status = OrderStatus::getByCode('sent');
        $this->status_id = $order_status->id;
        $this->status_setting_time = time();
        $this->updated_at = time();

        // всем заказам с неподтвержденной посадкой устанавливаем подтвержденность посадки
//        if(count($aNotConfirmTimeSat) > 0) {
//            Yii::$app->db->createCommand('UPDATE `order` SET confirmed_time_sat = ' . time() . ' WHERE id IN (' . implode(',', $aNotConfirmTimeSat) . ')')->execute();
//        }
        if(empty($this->confirmed_time_sat)) {
            $this->confirmed_time_sat = time();
        }

        // Когда заказы переходят в статус "Отправлен", то в таблице клиентов пересчитываются: order_count++, prize_trip_count?++
        $client = $this->client;

        if($this->prize_trip_count > 0) {
            $client->current_year_sended_prize_places += $this->prize_trip_count;
            $client->setField('current_year_sended_prize_places', $client->current_year_sended_prize_places);
        }
        if($this->informerOffice != null && $this->informerOffice->cashless_payment == 1) {

            $client->current_year_sended_informer_beznal_places += $this->places_count;
            $client->setField('current_year_sended_informer_beznal_places', $client->current_year_sended_informer_beznal_places);

            $client->current_year_sended_informer_beznal_orders += 1;
            $client->setField('current_year_sended_informer_beznal_orders', $client->current_year_sended_informer_beznal_orders);

        }elseif($this->is_not_places == 1) { // или счетчик "посылок" (нет места) инкрементируется

            $client->current_year_sended_isnotplaces_orders++;
            $client->setField('current_year_sended_isnotplaces_orders', $client->current_year_sended_isnotplaces_orders);

        }elseif($this->use_fix_price == 1) { // или увеличивается счетчик мест отправленных фикс. заказов

            $client->current_year_sended_fixprice_places += $this->places_count;
            $client->setField('current_year_sended_fixprice_places', $client->current_year_sended_fixprice_places);

            $client->current_year_sended_fixprice_orders += 1;
            $client->setField('current_year_sended_fixprice_orders', $client->current_year_sended_fixprice_orders);
        }

        if($client != null) {
            $client->recountSendedCanceledReliabilityCounts($this, 1, $this->places_count, 0 , 0);
        }


        // "логируем" данные
        // $day_report_trip_transport = new DayReportTripTransport();
        // - тут идет куча пересчета
//        $day_report_trip_transport = DayReportTripTransport::find()
//            ->where(['trip_transport_id' => $this->fact_trip_transport_id])
//            ->one();
//        if($day_report_trip_transport == null) {
//            throw new ForbiddenHttpException('Отчет дня связанный с т/с заказа - не найден');
//        }

        $day_report_trip_transport->places_count_sent += $this->places_count;
        $day_report_trip_transport->child_count_sent += $this->child_count;
        $day_report_trip_transport->student_count_sent += $this->student_count;
        $day_report_trip_transport->prize_trip_count_sent += $this->prize_trip_count;
        $day_report_trip_transport->bag_count_sent += $this->bag_count;
        $day_report_trip_transport->suitcase_count_sent += $this->suitcase_count;
        $day_report_trip_transport->oversized_count_sent += $this->oversized_count;
        $day_report_trip_transport->is_not_places_count_sent += $this->is_not_places;
        $day_report_trip_transport->proceeds += $this->price;
        $day_report_trip_transport->paid_summ += $this->paid_summ;

        $informer_office = InformerOffice::find()->where(['code' => 'without_record'])->one();
        if($informer_office == null) {
            throw new ForbiddenHttpException('Источник "Без записи" не найден');
        }
        if($this->informer_office_id == $informer_office->id) {
            $day_report_trip_transport->no_record++;
        }

        $yandexPointTo = $this->yandexPointTo;
        $yandexPointFrom = $this->yandexPointFrom;
        if(
            ($yandexPointTo != null && $yandexPointTo->alias == 'airport')
            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта
            $day_report_trip_transport->airport_count_sent++;
        }

        if($this->use_fix_price == 1) {
            $day_report_trip_transport->fix_price_count_sent++;
        }

        if(!$day_report_trip_transport->save(false)) {
            throw new ErrorException('Не удалось сохранить информацию в отчет отображаемого дня');
        }

        // записываем в "круги" отправленную машину
        $trip_start_time = $trip->date + Helper::convertHoursMinutesToSeconds($trip->start_time);
        $transport_circle = DayReportTransportCircle::find()
            ->where(['transport_id' => $this->factTripTransport->id, 'state' => 0])
            ->andWhere(['<', 'base_city_trip_start_time', $trip_start_time])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        if($transport_circle == null) {
            throw new ForbiddenHttpException('Цикл транспорта не найден');
        }
        $transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
        $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;
        if(!$transport_circle->save()) {
            throw new ForbiddenHttpException('Не удалось сохранить запись машины в таблице кругов');
        }

        // логируем отправленные заказы OrderReport
        // запись в OrderReport::tableName(),
        $aOrdersReports[] = [
            'day_report_trip_transport_id' => $day_report_trip_transport->id,
            'date_sended' => $day_report_trip_transport->date,
            'order_id' => $this->id,
            'client_id' => $this->client_id,
            'client_name' => ($this->client != null ? $this->client->name : ''),
            'date' => $this->date,
            'direction_id' => $this->direction_id,
            'direction_name' => ($direction != null ? $direction->sh_name : ''),

            'street_id_from' => $this->street_id_from,
            'street_from_name' => ($this->streetFrom != null ? $this->streetFrom->name : ''),
            'point_id_from' => $this->point_id_from,
            'point_from_name' => ($this->pointFrom != null ? $this->pointFrom->name : ''),

            'yandex_point_from_id' => $this->yandex_point_from_id,
            'yandex_point_from_name' => $this->yandex_point_from_name,
            'yandex_point_from_lat' => $this->yandex_point_from_lat,
            'yandex_point_from_long' => $this->yandex_point_from_long,

            'time_air_train_arrival' => $this->time_air_train_arrival,
            'street_id_to' => $this->street_id_to,
            'street_to_name' => ($this->streetTo != null ? $this->streetTo->name : ''),
            'point_id_to' => $this->point_id_to,
            'point_to_name' => ($this->pointTo != null ? $this->pointTo->name : ''),

            'yandex_point_to_id' => $this->yandex_point_to_id,
            'yandex_point_to_name' => $this->yandex_point_to_name,
            'yandex_point_to_lat' => $this->yandex_point_to_lat,
            'yandex_point_to_long' => $this->yandex_point_to_long,

            'time_air_train_departure' => $this->time_air_train_departure,
            'trip_id' => $this->trip_id,
            'trip_name' => ($this->trip != null ? $this->trip->name : ''),
            'informer_office_id' => $this->informer_office_id,
            'informer_office_name' => ($this->informerOffice != null ? $this->informerOffice->name : ''),
            'is_not_places' => $this->is_not_places,
            'places_count' => $this->places_count,
            'student_count' => $this->student_count,
            'child_count' => $this->child_count,
            'bag_count' => $this->bag_count,
            'suitcase_count' => $this->suitcase_count,
            'oversized_count' => $this->oversized_count,
            'prize_trip_count' => $this->prize_trip_count,
            'comment' => $this->comment,
            'additional_phone_1' => $this->additional_phone_1,
            'additional_phone_2' => $this->additional_phone_2,
            'additional_phone_3' => $this->additional_phone_3,
            'time_sat' => $this->time_sat,
            'use_fix_price' => $this->use_fix_price,
            'price' => $this->price,
            'time_confirm' => $this->time_confirm,
            // 'time_vpz' => $fact_order->time_vpz, - это и есть поле first_writedown_click_time
            'is_confirmed' => $this->is_confirmed,
            'first_writedown_click_time' => $this->first_writedown_click_time,
            'first_writedown_clicker_id' => $this->first_writedown_clicker_id,
            'first_writedown_clicker_name' => ($this->firstWritedownClicker != null ? $this->firstWritedownClicker->fio : ''),
            'first_confirm_click_time' => $this->first_confirm_click_time,
            'first_confirm_clicker_id' => $this->first_confirm_clicker_id,
            'first_confirm_clicker_name' => ($this->firstConfirmClicker != null ? $this->firstConfirmClicker->fio : ''),
            'radio_confirm_now' => $this->radio_confirm_now,
            'radio_group_1' => $this->radio_group_1,
            'radio_group_2' => $this->radio_group_2,
            'radio_group_3' => $this->radio_group_3,
            'confirm_selected_transport' => $this->confirm_selected_transport,
            'fact_trip_transport_id' => $this->fact_trip_transport_id,
            'fact_trip_transport_car_reg' => ($transport != null ? $transport->car_reg : ''),
            'fact_trip_transport_color' => ($transport != null ? $transport->color : ''),
            'fact_trip_transport_model' => ($transport != null ? $transport->model : ''),
            'fact_trip_transport_driver_id' => ($driver != null ? $driver->id : ''),
            'fact_trip_transport_driver_fio' => ($driver != null ? $driver->fio : ''),
            'has_penalty' => $this->has_penalty,
            'relation_order_id' => $this->relation_order_id,
        ];

        Yii::$app->db->createCommand()->BatchInsert(
            OrderReport::tableName(),
            [
                'day_report_trip_transport_id',
                'date_sended',
                'order_id',
                'client_id',
                'client_name',
                'date',
                'direction_id',
                'direction_name',

                'street_id_from',
                'street_from_name',
                'point_id_from',
                'point_from_name',

                'yandex_point_from_id',
                'yandex_point_from_name',
                'yandex_point_from_lat',
                'yandex_point_from_long',

                'time_air_train_arrival',

                'street_id_to',
                'street_to_name',
                'point_id_to',
                'point_to_name',

                'yandex_point_to_id',
                'yandex_point_to_name',
                'yandex_point_to_lat',
                'yandex_point_to_long',

                'time_air_train_departure',
                'trip_id',
                'trip_name',
                'informer_office_id',
                'informer_office_name',
                'is_not_places',
                'places_count',
                'student_count',
                'child_count',
                'bag_count',
                'suitcase_count',
                'oversized_count',
                'prize_trip_count',
                'comment',
                'additional_phone_1',
                'additional_phone_2',
                'additional_phone_3',
                'time_sat',
                'use_fix_price',
                'price',
                'time_confirm',
                //'time_vpz',
                'is_confirmed',
                'first_writedown_click_time',
                'first_writedown_clicker_id',
                'first_writedown_clicker_name',
                'first_confirm_click_time',
                'first_confirm_clicker_id',
                'first_confirm_clicker_name',
                'radio_confirm_now',
                'radio_group_1',
                'radio_group_2',
                'radio_group_3',
                'confirm_selected_transport',
                'fact_trip_transport_id',
                'fact_trip_transport_car_reg',
                'fact_trip_transport_color',
                'fact_trip_transport_model',
                'fact_trip_transport_driver_id',
                'fact_trip_transport_driver_fio',
                'has_penalty',
                'relation_order_id',
            ],
            $aOrdersReports
        )->execute();

    }*/

    public function sendWithTransport() {

        // отправляем заказ
        $order_status = OrderStatus::getByCode('sent');
        Yii::$app->db->createCommand('UPDATE `order` SET status_id = ' . $order_status->id . ', status_setting_time=' . time() . ', updated_at='.time().' WHERE id = '.$this->id)->execute();

        // если у всех т/с на рейсе закончились места, то устанавливаем рейсу  has_free_places = 0
        // проверяем есть ли свободные места в отправленных т/с
        if($this->trip_id > 0) {

            $sended_trip_transports = TripTransport::find()
                ->where(['trip_id' => $this->trip_id])
                ->all();

            $has_free_places = false;
            foreach ($sended_trip_transports as $trip_transport) {
//                $transport = $trip_transport->transport;
//                if ($transport->places_count > $trip_transport->factKZMPlacesCount) {
//                    $has_free_places = true;
//                    break;
//                }
                if($trip_transport->used_places_count < $trip_transport->total_places_count) {
                    $has_free_places = true;
                    break;
                }
            }

            if($has_free_places == false) {
                $this->trip->setField('has_free_places', 0);
            }
        }
    }
}
