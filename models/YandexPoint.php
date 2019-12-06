<?php

namespace app\models;

use ErrorException;
use Yii;
use app\models\City;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "yandex_point".
 *
 * @property integer $id
 * @property string $name
 * @property integer $city_id
 * @property double $lat
 * @property double $long
 */
class YandexPoint extends \yii\db\ActiveRecord
{
    const MIN_POINTS_DISTANCE = 40; // минимальная дистанция между точками

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yandex_point';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'lat', 'long'], 'required'],
            [['city_id', 'creator_id', 'updater_id', 'created_at', 'updated_at', 'sync_date', 'super_tariff_used'], 'integer'],
            [['lat', 'long'], 'number'],
            [['external_use'], 'boolean'],
            [['name', 'description'], 'string', 'max' => 255],
            [['alias'], 'string', 'max' => 10],
            [['name'], 'unique'],
            [['lat', /*'long'*/], 'checkLatLong', 'skipOnEmpty' => false],
            [['critical_point', 'point_of_arrival', 'popular_departure_point', 'popular_arrival_point'], 'boolean'],
            [['time_to_get_together_short', 'time_to_get_together_long'], 'safe']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['set_time_to_get_together'] = [
            'time_to_get_together_short',
            'time_to_get_together_long',
            'sync_date',
        ];

        return $scenarios;
    }



    public function checkLatLong($attribute, $params)
    {
        if(!empty($this->lat) && $this->lat > 0 && !empty($this->long) && $this->long > 0) {

//            if($this->id > 0) {
//                $yandex_point = YandexPoint::find()
//                    ->where(['lat' => $this->lat, 'long' => $this->long])
//                    ->andWhere(['!=', 'id', $this->id])
//                    ->one();
//            }else {
//                $yandex_point = YandexPoint::find()
//                    ->where(['lat' => $this->lat, 'long' => $this->long])
//                    ->one();
//            }
//            if($yandex_point != null) {
//                $this->addError($attribute, 'Поля «ширина» и «долгота» должны быть уникальны');
//            }

            if ($this->isNewRecord) {
                $yandex_points = YandexPoint::find()
                    ->where(['city_id' => $this->city_id])
                    ->all();
            }else {
                $yandex_points = YandexPoint::find()
                    ->where(['city_id' => $this->city_id])
                    ->andWhere(['!=', 'id', $this->id])
                    ->all();
            }

            foreach($yandex_points as $yandex_point) {
                $distance = YandexPoint::getDistance($this->lat, $this->long, $yandex_point->lat, $yandex_point->long);
                if($distance <= self::MIN_POINTS_DISTANCE) {
                    $this->addError($attribute, 'Расстоянием между точками должно быть больше '.self::MIN_POINTS_DISTANCE.' метров');
                }
            }
        }
    }

    // Функция возвращает расстояние в метрах между координатами двух точек
    public static function getDistance($lat1, $lon1, $lat2, $lon2) {

        $lat1 *= M_PI / 180;
        $lat2 *= M_PI / 180;
        $lon1 *= M_PI / 180;
        $lon2 *= M_PI / 180;

        $d_lon = $lon1 - $lon2;

        $slat1 = sin($lat1);
        $slat2 = sin($lat2);
        $clat1 = cos($lat1);
        $clat2 = cos($lat2);
        $sdelt = sin($d_lon);
        $cdelt = cos($d_lon);

        $y = pow($clat2 * $sdelt, 2) + pow($clat1 * $slat2 - $slat1 * $clat2 * $cdelt, 2);
        $x = $slat1 * $slat2 + $clat1 * $clat2 * $cdelt;

        return atan2(sqrt($y), $x) * 6372795;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_use' => 'Внешнее использование (да/нет)',
            'name' => 'Название',
            'city_id' => 'Город',
            'lat' => 'Широта',
            'long' => 'Долгота',
            'point_of_arrival' => 'Является точкой прибытия',
            'critical_point' => 'Критическая точка',
            'super_tariff_used' => 'Применяется супер тариф',
            'popular_departure_point' => 'Популярная точка отправления',
            'popular_arrival_point' => 'Популярная точка прибытия',
            'alias' => 'Доп.поле означающее принадлежность точки к чему-либо, например к аэропорту',
            'description' => 'Описание',
            'time_to_get_together_short' => 'Относительное время от ВРПТ до конечной базовой точки рейса коротких рейсов',
            'time_to_get_together_long' => 'Относительное время от ВРПТ до конечной базовой точки рейса длинных рейсов',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'creator_id' => 'Создатель точки',
            'updater_id' => 'Редактор точки',
            'sync_date' => 'Дата синхронизации с клиенским сервером'
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->creator_id = Yii::$app->user->getId();
        }else {
            if($this->scenario != 'set_time_to_get_together') {
                $this->updated_at = time();
                $this->updater_id = Yii::$app->user->getId();
            }
        }

        $this->sync_date = null;

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        // если ориентир(точка) используется в заказе, то прерывается удаление
        $order = Order::find()->where([
            'OR',
            ['yandex_point_from_id' => $this->id],
            ['yandex_point_to_id' => $this->id]
        ])->one();

        if($order != null) {
            throw new ForbiddenHttpException('Нельзя удалить яндекс-точку, которая использована в заказах');
        }

        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getCreator() {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }


    public function getUpdater() {
        return $this->hasOne(User::className(), ['id' => 'updater_id']);
    }

    public static function recountTimeToGetTogether($p_AK, $p_KA, $max_time_short_trip_AK, $max_time_short_trip_KA) {

        ini_set('memory_limit', '-1');

        $pointsTimeToGetTogetherLong = []; // относительно время короткого сбора
        $pointsTimeToGetTogetherShort = []; // относительно время длинного сбора

//        $p_AK = 6;
//        $p_KA = 2;
//        $max_time_short_trip_AK = 40*60;
//        $max_time_short_trip_KA = 30*60;

        $sql = "UPDATE yandex_point SET time_to_get_together_short=NULL, time_to_get_together_long=NULL;";
        Yii::$app->db->createCommand($sql)->execute();

        $sent_order_status = OrderStatus::getByCode('sent');
        $yandex_points = YandexPoint::find()->all(); // 0.0391 сек.

        $orders = Order::find()
            ->where(['status_id' => $sent_order_status->id])
            ->andWhere(['>', 'time_confirm', 0])
            ->andWhere(['>', 'yandex_point_from_id', 0])
            //->andWhere(['>', 'trip_id', 0])
            ->all(); // 8.4438 сек.
        $trips = Trip::find()
            ->where(['id' => ArrayHelper::map($orders, 'trip_id', 'trip_id')])
            ->all(); // 0.3538 сек.
        $aTrips = ArrayHelper::index($trips, 'id');

        // делю рейсы на котороткие и длинные


        // для каждой яндекс-точки отправки группирую заказы по рейсам:  [id_точки_отправки][id_рейса] => массив заказов
        $aYandexPointsOrders = [];
        foreach($orders as $order) {
            $aYandexPointsOrders[$order->yandex_point_from_id][$order->trip_id][$order->id] = $order;
        }

        //echo "count_241=".count($aYandexPointsOrders[241])."\n";
        //echo "241:<pre>"; print_r($aYandexPointsOrders[241]); echo "</pre>";

        foreach($aYandexPointsOrders as $yandex_point_from_id => $aTripsOrders) {

            foreach($aTripsOrders as $trip_id => $aTripOrders) {

                $trip = $aTrips[$trip_id];
                if($trip->direction_id == 1) { // АК
                    if(count($aTripOrders) >= $p_AK) {

                        // перебираю заказы для нахождения разницы между ВРПТ и конечной базовой точкой рейса
                        $aTripEnd = explode(':', $trip->end_time);
                        $trip_end_time_secs = 3600 * intval($aTripEnd[0]) + 60 * intval($aTripEnd[1]);

                        // определею этот рейс - длинный или короткий
                        $aTripStart = explode(':', $trip->start_time);
                        $trip_start_time_secs = 3600 * intval($aTripStart[0]) + 60 * intval($aTripStart[1]);

                        foreach ($aTripOrders as $order_id => $order) {

                            // возможно $order->date - это не начало дня, а начало дня + какое-то время рейса?...
                            $time_to_get_together = $trip_end_time_secs - ($order->time_confirm - $order->date);

                            if ($trip_end_time_secs - $trip_start_time_secs <= $max_time_short_trip_AK) { // короткий рейс
                                $pointsTimeToGetTogetherShort[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            } else { // длинный рейс
                                $pointsTimeToGetTogetherLong[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }
                        }
                    }


                }else { // КА
                    if(count($aTripOrders) >= $p_KA) {

                        // перебираю заказы для нахождения разницы между ВРПТ и конечной базовой точкой рейса
                        $aTripEnd = explode(':', $trip->end_time);
                        $trip_end_time_secs = 3600 * intval($aTripEnd[0]) + 60 * intval($aTripEnd[1]);

                        // определею этот рейс - длинный или короткий
                        $aTripStart = explode(':', $trip->start_time);
                        $trip_start_time_secs = 3600 * intval($aTripStart[0]) + 60 * intval($aTripStart[1]);

                        foreach ($aTripOrders as $order_id => $order) {

                            //echo "order:<pre>"; print_r($order); echo "</pre>"; exit;

                            // возможно $order->date - это не начало дня, а начало дня + какое-то время рейса?...
                            $time_to_get_together = $trip_end_time_secs - ($order->time_confirm - $order->date);
                            if ($trip_end_time_secs - $trip_start_time_secs <= $max_time_short_trip_KA) { // короткий рейс
                                $pointsTimeToGetTogetherShort[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            } else { // длинный рейс
                                $pointsTimeToGetTogetherLong[$yandex_point_from_id][$time_to_get_together][$order_id] = 1;
                            }
                        }
                    }
                }
            }
        }


        // ищу для каждой точки наиболее часто встречающиеся относительно время
        foreach($yandex_points as $yandex_point) {

            if(isset($pointsTimeToGetTogetherShort[$yandex_point->id])) {

                $max_count_point_orders = 0;
                $max_count_time_to_get_together = 0;
                foreach($pointsTimeToGetTogetherShort[$yandex_point->id] as $time_to_get_together => $pointOrders) {
                    if(count($pointOrders) > $max_count_point_orders) {
                        $max_count_point_orders = count($pointOrders);
                        $max_count_time_to_get_together = $time_to_get_together;
                    }
                }

                if($max_count_point_orders > 0) {
                    $yandex_point->time_to_get_together_short = $max_count_time_to_get_together;
                }

            }else {
                //echo "для точки ".$yandex_point->id." нет коротких рейсов\n";
            }

            if(isset($pointsTimeToGetTogetherLong[$yandex_point->id])) {

                $max_count_point_orders = 0;
                $max_count_time_to_get_together = 0;
                foreach($pointsTimeToGetTogetherLong[$yandex_point->id] as $time_to_get_together => $pointOrders) {
                    if(count($pointOrders) > $max_count_point_orders) {
                        $max_count_point_orders = count($pointOrders);
                        $max_count_time_to_get_together = $time_to_get_together;
                    }
                }

                if($max_count_point_orders > 0) {
                    $yandex_point->time_to_get_together_long = $max_count_time_to_get_together;
                }

            }else {
                //echo "для точки ".$yandex_point->id." нет длинных рейсов\n";
            }

            $yandex_point->sync_date = null;
            $yandex_point->scenario = 'set_time_to_get_together';
            if (!$yandex_point->save(false)) {
                throw new ErrorException('Не удалось сохранить яндекс-точку');
            }
        }
    }
}
