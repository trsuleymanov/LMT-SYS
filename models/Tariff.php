<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;


class Tariff extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['common_price', 'student_price', 'baby_price', 'aero_price', 'parcel_price', 'loyal_price'], 'number'],
//            [['commercial'], 'integer'],
//            [['start_date'], 'safe']

            [[
                'unprepayment_common_price', 'unprepayment_student_price', 'unprepayment_baby_price',
                'unprepayment_aero_price', 'unprepayment_parcel_price', 'unprepayment_loyal_price', 'unprepayment_reservation_cost',

                'prepayment_common_price', 'prepayment_student_price', 'prepayment_baby_price',
                'prepayment_aero_price', 'prepayment_parcel_price', 'prepayment_loyal_price', 'prepayment_reservation_cost',

                'superprepayment_common_price', 'superprepayment_student_price', 'superprepayment_baby_price',
                'superprepayment_aero_price', 'superprepayment_parcel_price', 'superprepayment_loyal_price', 'superprepayment_reservation_cost',

            ], 'number'],
            [['commercial', 'sync_date'], 'integer'],
            [['start_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Дата запуска',
//            'common_price' => 'Общий',
//            'student_price' => 'Студенческий ',
//            'baby_price' => 'Детский',
//            'aero_price' => 'Аэропорт',
//            'parcel_price' => 'Посылка (без места)',
//            'loyal_price' => 'Призовая поездка',

            'commercial' => 'Спец. тариф (коммерческий)',

            'unprepayment_common_price' => 'Общая стоимость проезда без предоплаты',
            'unprepayment_student_price' => 'Стоимость студенческого проезда без предоплаты',
            'unprepayment_baby_price' => 'Стоимость детского проезда без предоплаты',
            'unprepayment_aero_price' => 'Стоимость поездки в/из аэропорта без предоплаты',
            'unprepayment_parcel_price' => 'Стоимость провоза посылки (без места) без предоплаты',
            'unprepayment_loyal_price' => 'Стоимость призовой поездки без предоплаты',
            'unprepayment_reservation_cost' => 'Стоимость бронирования без предоплаты',

            'prepayment_common_price' => 'Общая стоимость проезда с предоплатой',
            'prepayment_student_price' => 'Стоимость студенческого проезда с предоплатой',
            'prepayment_baby_price' => 'Стоимость детского проезда с предоплатой',
            'prepayment_aero_price' => 'Стоимость поездки в/из аэропорта с предоплатой',
            'prepayment_parcel_price' => 'Стоимость провоза посылки (без места) с предоплатой',
            'prepayment_loyal_price' => 'Стоимость призовой поездки с предоплатой',
            'prepayment_reservation_cost' => 'Стоимость бронирования с предоплатой',

            'superprepayment_common_price' => 'Общая стоимость проезда с супер-предоплатой',
            'superprepayment_student_price' => 'Стоимость студенческого проезда с супер-предоплатой',
            'superprepayment_baby_price' => 'Стоимость детского проезда с супер-предоплатой',
            'superprepayment_aero_price' => 'Стоимость поездки в/из аэропорта с супер-предоплатой',
            'superprepayment_parcel_price' => 'Стоимость провоза посылки (без места) с супер-предоплатой',
            'superprepayment_loyal_price' => 'Стоимость призовой поездки с супер-предоплатой',
            'superprepayment_reservation_cost' => 'Стоимость бронирования с супер-предоплатой',

            'sync_date' => 'Дата синхронизации'
        ];
    }


    /*
     * Поиск подходящего тарифа по дате использования тарифа
     */
    public function beforeSave($insert)
    {
        if(isset($this->start_date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->start_date)) {
            $this->start_date = strtotime($this->start_date);   // convent '07.11.2016' to unixtime
        }

        return parent::beforeSave($insert);
    }


//    public static function getTariffByDate($unixdate) {
//
//        return Tariff::find()->where(['<=', 'start_date', $unixdate])->orderBy(['start_date' => SORT_DESC])->one();
//    }

    /*
     * Функция возвращает заказы с датой реализации от $unixdate и не превышающие дату начала действия следующего тарифа
     */
    public function getFutureOrders()
    {
        //$start_date = (time() > $this->start_date ? time() : $this->start_date);
        $start_date = $this->start_date;
        $next_tariff = Tariff::find()
            ->where(['>','start_date', $start_date])
            ->andWhere(['commercial' => $this->commercial]) // для не ком-х тарифов смотрим некоммерческие и наоборот
            ->orderBy(['start_date' => SORT_ASC])
            ->one();

//        if($next_tariff != null) {
//            $new_start_date = (time() > $next_tariff->start_date ? time() : $next_tariff->start_date);
//        }else {
//            $new_start_date = time();
//        }


        // нахождение рейсов коммерческих или не коммерческих начиная от даты запуска текущего тарифа
        // или от сегодня и до даты следующего подобного рейса (если следущий подобный рейс есть)
        $start_date = ($this->start_date > strtotime(date('d.m.Y')) ? $this->start_date : strtotime(date('d.m.Y')));

        $trips_query = Trip::find()->andWhere(['>=', 'date', $start_date]);
        if($this->commercial == true) {
            $trips_query->andWhere(['commercial' => 1]);
        }else {
            $trips_query->andWhere([
                'or',
                ['commercial' => intval($this->commercial)],
                ['commercial' =>  NULL]
            ]);
        }
        if($next_tariff != null) {
            $trips_query->andWhere(['<', 'date', $next_tariff->start_date]);

        }
        $trips = $trips_query->all();


        $created_order_status = OrderStatus::getByCode('created');
        if(count($trips) > 0) {
            return $orders = Order::find()
                ->where(['IN', 'trip_id', ArrayHelper::map($trips, 'id', 'id')])
                ->andWhere(['use_fix_price' => 0])
                ->andWhere(['status_id' => $created_order_status->id])
                ->all();
        }else {
            return $orders = Order::find()
                ->andWhere(['use_fix_price' => 0])
                ->andWhere(['status_id' => $created_order_status->id])
                ->all();
        }

//        $orders = Order::find()
//            ->where(['>=', 'date', $start_date])
//            ->andWhere(['use_fix_price' => 0])
//            ->andWhere(['status_id' => $created_order_status->id]);
//        if($next_tariff != null) {
//            $orders->andWhere(['<', 'date', $next_tariff->start_date]);
//        }

//        return $orders->all();
    }
}
