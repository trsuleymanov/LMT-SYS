<?php

namespace app\models;

use app\components\Helper;
use Yii;
use app\models\Point;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "client".
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile_phone
 * @property string $home_phone
 * @property string $alt_phone
 * @property integer $rating
 * @property integer $sended_prize_trip_count
 * @property integer $created_at
 * @property integer $updated_at
 */
class Client extends \yii\db\ActiveRecord
{
    public $mobile_phone_new;
    public $name_new;
    public $home_phone_new;
    public $alt_phone_new;


    public static function getClientByMobilePhone($mobile_phone)
    {
        $mobile_phone = trim($mobile_phone);

        if(empty($mobile_phone)) {
            return null;
        }

        if($mobile_phone[0] != '+') {
            $mobile_phone = '+' . $mobile_phone;
        }

        $client = Client::find()->where(['mobile_phone' => $mobile_phone])->one();
        if($client == null) {
            throw new ErrorException('клиент не найден. Телефон: '.$mobile_phone);
        }

        return $client;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile_phone', 'name'], 'required'],
            [[
                'sended_orders_places_count', 'rating', 'sended_prize_trip_count', 'penalty', 'created_at',
                'updated_at', 'sended_fixprice_orders_places_count', 'sended_informer_beznal_orders_places_count', 'canceled_orders_places_count',
                'sended_is_not_places_order_count', 'do_tariff_id',

                'current_year_sended_places', 'current_year_sended_orders', 'current_year_canceled_places',
                'current_year_canceled_orders',
                'current_year_canceled_orders_1h', 'current_year_canceled_orders_12h',
                'current_year_sended_prize_places', 'current_year_penalty', 'current_year_sended_fixprice_places',
                'current_year_sended_fixprice_orders', 'current_year_sended_informer_beznal_places',
                'current_year_sended_informer_beznal_orders', 'current_year_sended_isnotplaces_orders',

                'past_years_sended_places', 'past_years_sended_orders', 'past_years_canceled_places',
                'past_years_canceled_orders',
                'past_years_canceled_orders_1h', 'past_years_canceled_orders_12h',
                'past_years_sended_prize_places', 'past_years_penalty',
                'past_years_sended_fixprice_places', 'past_years_sended_fixprice_orders',
                'past_years_sended_informer_beznal_places', 'past_years_sended_informer_beznal_orders',
                'past_years_sended_isnotplaces_orders',
                'sync_date',

            ], 'integer'],

            //[['current_year_places_reliability', 'current_year_orders_reliability',], 'number'],

            ['email', 'email'],
            [['name'], 'string', 'max' => 255, 'min' => 3],
            [['mobile_phone', 'home_phone', 'alt_phone'], 'checkPhone'],
            [['mobile_phone', 'email'], 'unique'],
            [['cashback'], 'safe']
        ];
    }

    public function checkPhone($attribute)
    {
        if (!preg_match('/^\+7\-[0-9]{3}\-[0-9]{3}\-[0-9]{4}$/', $this->$attribute)) {
            $this->addError($attribute, 'Дата должна быть в формате +7-***-***-****');
        }else {
            return true;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
            'do_tariff_id' => 'Признак формирования цены',
            'mobile_phone' => 'Мобильный телефон',
            'home_phone' => 'Домашний телефон',
            'alt_phone' => 'Дополнительный телефон',
            'cashback' => 'Кэш-бэк счет',
            'sended_orders_places_count' => 'Количество мест в отправленных заказах',
            'canceled_order_count' => 'Количество мест в отмененных заказах',
            'rating' => 'Рейтинг',
            'sended_prize_trip_count' => 'Количество призовых поездок',
            'sended_fixprice_orders_places_count' => 'Количество мест в отправленных заказах с фикс.ценой',
            'sended_informer_beznal_orders_places_count' => 'Количество мест в отправленных заказах где выбрана информаторская с безналичной оплатой',


            'current_year_sended_places' => 'Число отправленных мест',
            'current_year_sended_orders' => 'Число отправленных заказов',
            'current_year_canceled_places' => 'Число отмененных мест',
            'current_year_canceled_orders' => 'Число отмененных заказов',

            'current_year_canceled_orders_1h' => 'В текущем году: количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса',
            'current_year_canceled_orders_12h' => 'В текущем году: количество отмененных заказов менее чем за 12 часов до последней точки рейса',

            //'current_year_places_reliability' => 'Надежность по местам в текущем году',
            //'current_year_orders_reliability' => 'Надежность по заказам в текущем году',

            'current_year_sended_prize_places' => 'Число отправленных призовых поездок в текущем году',
            'current_year_penalty' => 'Число штрафов в текущем году',
            'current_year_sended_fixprice_places' => 'Число мест по фикс.цене отправленных в текущем году',
            'current_year_sended_fixprice_orders' => 'Число заказов по фикс.цене в текущем году',
            'current_year_sended_informer_beznal_places' => 'Число мест с безналичной оплатой в текущем году',
            'current_year_sended_informer_beznal_orders' => 'Число заказов с безналичной оплатой в текущем году',
            'current_year_sended_isnotplaces_orders' => 'Число посылок в текущем году',

            'past_years_sended_places' => 'Число отправленных мест всего по прошлым периодам',
            'past_years_sended_orders' => 'Число отмененных мест всего по прошлым периодам',
            'past_years_canceled_places' => 'Число отправленных заказов по прошлым периодам',
            'past_years_canceled_orders' => 'Число отмененных заказов по прошлым периодам',

            'past_years_canceled_orders_1h' => 'За прошлые годы: количество отмененных заказов менее чем за 1 час до первой точки рейса или на протяжении 3 часов после рейса',
            'past_years_canceled_orders_12h' => 'За прошлые годы: количество отмененных заказов менее чем за 12 часов до последней точки рейса',

            'past_years_sended_prize_places' => 'Количество отправленных призовых поездок по прошлым периодам',
            'past_years_penalty' => 'Количество штрафов по прошлым периодам',
            'past_years_sended_fixprice_places' => 'Количество мест по фикс.цене по прошлым периодам',
            'past_years_sended_fixprice_orders' => 'Количество заказов по фикс.цене по прошлым периодам',
            'past_years_sended_informer_beznal_places' => 'Количество мест с безналичной оплатой по прошлым периодам',
            'past_years_sended_informer_beznal_orders' => 'Количество заказов с безналичной оплатой по прошлым периодам',
            'past_years_sended_isnotplaces_orders' => 'Количество посылок по прошлым периодам',


            'penalty' => 'Количество штрафов',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'sync_date' => 'Дата синхронизации с клиенским сервером',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['client_id' => 'id']);
    }

    public function getDoTariff()
    {
        return $this->hasOne(DoTariff::className(), ['id' => 'do_tariff_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        $this->sync_date = null;

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if ($insert == true) {
            $advertising_source_report = AdvertisingSourceReport::find()
                ->where(['phone' => $this->mobile_phone])
                ->one();
            if($advertising_source_report != null) {
                $advertising_source_report->setField('client_id', $this->id);
            }
        }
    }


    public function recountSendedCanceledReliabilityCounts($order, $add_sended_orders_count = 0, $add_sended_places_count = 0, $add_canceled_orders_count = 0, $add_canceled_places_count = 0) {


        if($add_sended_orders_count != 0) {
            $this->current_year_sended_orders += $add_sended_orders_count;
        }
        if($add_sended_places_count != 0) {
            $this->current_year_sended_places += $add_sended_places_count;
        }
        if($add_canceled_orders_count != 0) {
            $this->current_year_canceled_orders += $add_canceled_orders_count;
        }
        if($add_canceled_places_count != 0) {
            $this->current_year_canceled_places += $add_canceled_places_count;
        }



        $trip = $order->trip;
        if($trip != null && $add_canceled_orders_count != 0 && $order->cancellation_click_time > 0) {

            list($trip_hours, $trip_mins) = explode(':', $trip->start_time);
            $trip_start_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

            if(
                ($trip_start_time - $order->cancellation_click_time < 3600)
                && ($trip_start_time - $order->cancellation_click_time > -10800)
            ) {
                $this->current_year_canceled_orders_1h += $add_canceled_orders_count;
            }

            // количество отмененных заказов менее чем за 12 часов до последней точки рейса
            list($trip_hours, $trip_mins) = explode(':', $trip->end_time);
            $trip_end_time = $trip->date + 3600*intval($trip_hours) + 60*intval($trip_mins);

            if($trip_end_time - $order->cancellation_click_time < 43200) { // 12 часов
                $this->current_year_canceled_orders_12h += $add_canceled_orders_count;
            }
        }


        $sql = 'UPDATE `client` SET
                  current_year_sended_orders='.$this->current_year_sended_orders.',
                  current_year_sended_places='.$this->current_year_sended_places.',
                  current_year_canceled_orders='.$this->current_year_canceled_orders.',
                  current_year_canceled_places='.$this->current_year_canceled_places.',
                  current_year_canceled_orders_1h='.intval($this->current_year_canceled_orders_1h).',
                  current_year_canceled_orders_12h='.intval($this->current_year_canceled_orders_12h).'
                  WHERE id='.$this->id;
        return Yii::$app->db->createCommand($sql)->execute();
    }

    public function getNearestOrdersMsg($current_order_date, $current_order_id = 0)
    {
        $date = strtotime($current_order_date);

        $min_date = strtotime(date('d.m.Y'));
        if($date - 2*86400 > $min_date) {
            $min_date = $date - 2*86400;
        }
        $max_date = $date + 2*86400;

        $orders = Order::find()
            ->where(['client_id' => $this->id])
            ->andWhere(['!=', 'id', intval($current_order_id)])
            ->andWhere(['>=', 'date', $min_date])
            ->andWhere(['<=', 'date', $max_date])
            ->andWhere(['>', 'status_id', 0])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        if(count($orders) == 0){
            return '';
        }else {
            $aOrdersMsg = [];
            foreach($orders as $order) {
                $trip = $order->trip;
                if($trip != null) {

                    if($trip->date == strtotime(date('d.m.Y'))) {
                        $class = 'orange-day-text';
                    }elseif($trip->date == strtotime(date('d.m.Y', time() + 86400))) {
                        $class = 'purple-day-text';
                    }else {
                        $class = '';
                    }

                    $aOrdersMsg[] = '<tr class="'.$class.'"><td><a target="_blank" href="/trip/trip-orders?trip_id='.$trip->id.'">'.Helper::getMainDate($trip->date, 3).'</a></td><td>'.$trip->direction->sh_name.' '.$trip->name.'</td><td>Мест: '.$order->places_count .'</td><td>'.$order->status->name.', '.($order->is_confirmed == 1 ? 'Пдт' : 'Не пдт').'</td></tr>';
                }
            }

//            $order_status_sent = OrderStatus::getByCode('sent');
//            $last_sent_order = Order::find()
//                ->where(['client_id' => $this->id, 'status_id' => $order_status_sent->id])
//                ->andWhere(['<=', 'date', time() - 86400])
//                ->orderBy(['date' => SORT_DESC])
//                ->one();
//            $message = '';
//            if($last_sent_order != null) {
//                $trip = $last_sent_order->trip;
//                $message .= "Последняя совершенная поездка:<br />".Helper::getMainDate($last_sent_order->date, 3).' '.$trip->direction->sh_name.' '.$trip->name.', Мест: '.$last_sent_order->places_count .', '.$last_sent_order->status->name.'<br /><br />';
//            }

            //return $message."Заказы клиента ".$this->name." (".$this->mobile_phone.") для проверки:<table id='client-last-orders-list' class='table' style='margin-top: 7px;'>".implode("", $aOrdersMsg).'</table>';
            return "Заказы клиента ".$this->name." (".$this->mobile_phone."):<table id='client-last-orders-list' class='table' style='margin-top: 7px;'>".implode("", $aOrdersMsg).'</table>';
        }
    }

    public function setField($field_name, $field_value)
    {
        if(!empty($field_value)) {
            $field_value = htmlspecialchars($field_value);
        }

        if($field_value === false) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = false WHERE id = '.$this->id;
        }elseif(empty($field_value)) {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = NULL WHERE id = '.$this->id;
        }else {
            $sql = 'UPDATE `'.self::tableName().'` SET '.$field_name.' = "'.$field_value.'" WHERE id = '.$this->id;
        }
        return Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
    }


    public function getLastOrderData($direction_id) {

        // ищеться последний заказ с данным клиентом и направлением, и если найден, то возвращаются точки заказа.
        $last_order = null;
        if($direction_id > 0 && $this->id > 0) {
            $last_order = Order::find()
                ->where(['client_id' => $this->id, 'direction_id' => $direction_id])
                ->orderBy(['date' => SORT_DESC, 'id' => SORT_DESC])
                ->one();
        }

//        $streetFrom = ($last_order != null ? $last_order->streetFrom : null);
//        $streetTo = ($last_order != null ? $last_order->streetTo : null);
//        $pointFrom = ($last_order != null ? $last_order->pointFrom : null);
//        $pointTo = ($last_order != null ? $last_order->pointTo : null);
//
//        $direction = Direction::findOne($direction_id);
//        if($direction->sh_name == 'АК') {
//            if($streetTo == null && $pointTo == null) {
//                $streetTo = Street::getDefaultStreet($direction->city_to);
//                $pointTo = null;
//            }
//        }elseif($direction->sh_name == 'КА') {
//            if($streetFrom == null && $pointFrom == null) {
//                $streetFrom = Street::getDefaultStreet($direction->city_from);
//                $pointFrom = null;
//            }
//            if($streetTo == null && $pointTo == null) {
//                $streetTo = Street::getDefaultStreet($direction->city_to);
//                $pointTo = Point::find()->where(['name' => "АВ - Автовокзал", 'city_id' => $direction->city_to])->one();
//            }
//        }

        $yandexPointFrom = ($last_order != null ? $last_order->yandexPointFrom : null);
        $yandexPointTo = ($last_order != null ? $last_order->yandexPointTo : null);


        $yandex_point_from_id = 0;
        $yandex_point_from_name = '';
        $yandex_point_from_lat = '';
        $yandex_point_from_long = '';
        if($last_order != null) {
            if($last_order->yandex_point_from_id > 0) {
                $yandexPointFrom = $last_order->yandexPointFrom;
                $yandex_point_from_id = $last_order->yandex_point_from_id;
                $yandex_point_from_name = $yandexPointFrom->name;
                $yandex_point_from_lat = $yandexPointFrom->lat;
                $yandex_point_from_long = $yandexPointFrom->long;
            }elseif(!empty($last_order->yandex_point_from_lat) && !empty($last_order->yandex_point_from_long) && !empty($last_order->yandex_point_from_name)) {
                $yandex_point_from_id = $last_order->yandex_point_from_id;
                $yandex_point_from_name = $last_order->yandex_point_from_name;
                $yandex_point_from_lat = $last_order->yandex_point_from_lat;
                $yandex_point_from_long = $last_order->yandex_point_from_long;
            }
        }

        $yandex_point_to_id = 0;
        $yandex_point_to_name = '';
        $yandex_point_to_lat = '';
        $yandex_point_to_long = '';
        if($last_order != null) {
            if($last_order->yandex_point_to_id > 0) {
                $yandexPointTo = $last_order->yandexPointTo;
                $yandex_point_to_id = $last_order->yandex_point_to_id;
                $yandex_point_to_name = $yandexPointTo->name;
                $yandex_point_to_lat = $yandexPointTo->lat;
                $yandex_point_to_long = $yandexPointTo->long;
            }elseif(!empty($last_order->yandex_point_to_lat) && !empty($last_order->yandex_point_to_long) && !empty($last_order->yandex_point_to_name)) {
                $yandex_point_to_id = $last_order->yandex_point_to_id;
                $yandex_point_to_name = $last_order->yandex_point_to_name;
                $yandex_point_to_lat = $last_order->yandex_point_to_lat;
                $yandex_point_to_long = $last_order->yandex_point_to_long;
            }
        }

        return [
            $last_order,
//            $streetFrom,
//            $pointFrom,
//            $streetTo,
//            $pointTo
            $yandexPointFrom,
            $yandex_point_from_id,
            $yandex_point_from_name,
            $yandex_point_from_lat,
            $yandex_point_from_long,

            $yandexPointTo,
            $yandex_point_to_id,
            $yandex_point_to_name,
            $yandex_point_to_lat,
            $yandex_point_to_long
        ];
    }


    public function getCurrentYear1hRejection() {

        $current_year_1h_rejection = 0;
        $total_current_orders = $this->current_year_sended_orders + $this->current_year_canceled_orders;
        if($total_current_orders > 0) {
            $current_year_1h_rejection =  100*round($this->current_year_canceled_orders_1h/$total_current_orders, 2);
        }

        return $current_year_1h_rejection;
    }

    public function getCurrentYear12hRejection() {

        $past_years_12h_rejection = 0;
        $total_current_orders = $this->current_year_sended_orders + $this->current_year_canceled_orders;
        if($total_current_orders > 0) {
            $past_years_12h_rejection = 100*round($this->current_year_canceled_orders_12h/$total_current_orders, 2);
        }

        return $past_years_12h_rejection;
    }

    public function getPastYears1hRejection() {

        $past_years_1h_rejection = 0;
        $total_past_orders = $this->past_years_sended_orders + $this->past_years_canceled_orders;
        if($total_past_orders > 0) {
            $past_years_1h_rejection =  100*round($this->past_years_canceled_orders_1h/$total_past_orders, 2);
        }

        return $past_years_1h_rejection;
    }

    public function getPastYears12hRejection() {

        $past_years_12h_rejection = 0;
        $total_past_orders = $this->past_years_sended_orders + $this->past_years_canceled_orders;
        if($total_past_orders > 0) {
            $past_years_12h_rejection =  100*round($this->past_years_canceled_orders_12h/$total_past_orders, 2);
        }

        return $past_years_12h_rejection;
    }



    public static function convertMobilePhone($mobile) {

        if(empty($mobile)) {
            return '';
        }

        $mobile_1 = substr($mobile, 0, 3);
        $mobile_2 = substr($mobile, 3, 3);
        $mobile_3 = substr($mobile, 6);

        return '+7-'.$mobile_1.'-'.$mobile_2.'-'.$mobile_3;
    }
}
