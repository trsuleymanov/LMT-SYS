<?php

namespace app\models;

use Yii;
use app\models\Direction;
use app\models\Order;
use app\models\ScheduleTrip;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

/**
 * Расписание
 *
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direction_id', 'name', 'start_date'], 'required'],
            [['direction_id', 'disabled_date'], 'integer'],
            [['name'], 'string', 'max' => 50],
            ['start_date', 'checkStartDate', 'skipOnEmpty' => false],
            //[['start_date'], 'safe']
        ];
    }

    public function checkStartDate($attribute, $params)
    {
        $tomorrow_time = strtotime(date('d.m.Y')) + 86400;
        $datetime = strtotime($this->start_date);
        if($datetime < $tomorrow_time) {
            $this->addError($attribute, 'Нельзя выбрать дату раньше '.date('d.m.Y', $tomorrow_time));
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
            'direction_id' => 'Направление',
            'name' => 'Название',
            'start_date' => 'Дата запуска расписания',
            'disabled_date' => 'Дата деактивации расписания',
        ];
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
    public function getScheduleTrips()
    {
        return $this->hasMany(ScheduleTrip::className(), ['schedule_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if(isset($this->start_date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->start_date)) {
            $this->start_date = strtotime($this->start_date);   // convent '07.11.2016' to unixtime
        }

        // удаляются действующие рейсы связанные с датами доступными текущему расписанию (кроме рейсов на дни которых уже существуют заказы)
        $this->deleteNotUsedTrips();

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createScheduleTrips();// создаются рейсы для нового расписания
        }

        // удаляются действующие рейсы связанные с датами доступными текущему расписанию (кроме рейсов на дни которых уже существуют заказы)
        $this->deleteNotUsedTrips();

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        // удаление рейсов расписания
        ScheduleTrip::deleteAll(['schedule_id' => $this->id]);

        // удаляются действующие рейсы связанные с датами доступными текущему расписанию (кроме рейсов на дни которых уже существуют заказы)
        $this->deleteNotUsedTrips();

        parent::afterDelete();
    }


    /*
     * Создание рейсов для текущего расписания
     */
    public function createScheduleTrips() {

        // найдем последнее расписание
        $prev_schedule = Schedule::find()
            ->where(['<','id', $this->id])
            ->andWhere(['direction_id' => $this->direction_id])
            ->orderBy(['start_date' => SORT_DESC])
            ->one();

        if($prev_schedule == null) { // по данному направлению расписаний предыдущих не существует, значит создается минимум рейсов

            $aNewScheduleTrips = [
                ['schedule_id' => $this->id, 'name' => '3:30', 'start_time' => '02:30', 'mid_time' => '03:00', 'end_time' => '03:30'],
                ['schedule_id' => $this->id, 'name' => '4:00', 'start_time' => '03:00', 'mid_time' => '03:30', 'end_time' => '04:00'],
                ['schedule_id' => $this->id, 'name' => '5:00', 'start_time' => '04:00', 'mid_time' => '04:30', 'end_time' => '05:00'],
                ['schedule_id' => $this->id, 'name' => '6:00', 'start_time' => '05:00', 'mid_time' => '05:30', 'end_time' => '06:00'],
                ['schedule_id' => $this->id, 'name' => '8:00', 'start_time' => '07:00', 'mid_time' => '07:30', 'end_time' => '08:00'],
                ['schedule_id' => $this->id, 'name' => '10:00', 'start_time' => '09:00', 'mid_time' => '09:30', 'end_time' => '10:00'],
                ['schedule_id' => $this->id, 'name' => '12:00', 'start_time' => '11:00', 'mid_time' => '11:30', 'end_time' => '12:00'],
                ['schedule_id' => $this->id, 'name' => '13:00', 'start_time' => '12:00', 'mid_time' => '12:30', 'end_time' => '13:00'],
                ['schedule_id' => $this->id, 'name' => '14:00', 'start_time' => '13:00', 'mid_time' => '13:30', 'end_time' => '14:00'],
                ['schedule_id' => $this->id, 'name' => '15:00', 'start_time' => '14:00', 'mid_time' => '14:30', 'end_time' => '15:00'],
                ['schedule_id' => $this->id, 'name' => '16:00', 'start_time' => '15:00', 'mid_time' => '15:30', 'end_time' => '16:00'],
                ['schedule_id' => $this->id, 'name' => '17:00', 'start_time' => '16:00', 'mid_time' => '16:30', 'end_time' => '17:00'],
                ['schedule_id' => $this->id, 'name' => '18:00', 'start_time' => '17:00', 'mid_time' => '17:30', 'end_time' => '18:00'],
                ['schedule_id' => $this->id, 'name' => '19:00', 'start_time' => '18:00', 'mid_time' => '18:30', 'end_time' => '19:00'],
                ['schedule_id' => $this->id, 'name' => '20:00', 'start_time' => '19:00', 'mid_time' => '19:30', 'end_time' => '20:00'],
                ['schedule_id' => $this->id, 'name' => '21:00', 'start_time' => '20:00', 'mid_time' => '20:30', 'end_time' => '21:00'],
            ];

            return Yii::$app->db->createCommand()->BatchInsert(
                ScheduleTrip::tableName(),
                ['schedule_id', 'name', 'start_time', 'mid_time' ,'end_time'],
                $aNewScheduleTrips
            )->execute();

        }else {
            $prev_schedule_trips = $prev_schedule->scheduleTrips;
            $aNewScheduleTrips = [];
            foreach($prev_schedule_trips as $schedule_trip) {
                $aNewScheduleTrips[] = [
                    'schedule_id' => $this->id,
                    'name' => $schedule_trip->name,
                    'start_time' => $schedule_trip->start_time,
                    'mid_time' => $schedule_trip->mid_time,
                    'end_time' => $schedule_trip->end_time
                ];
            }

            return Yii::$app->db->createCommand()->BatchInsert(
                ScheduleTrip::tableName(),
                ['schedule_id', 'name', 'start_time', 'mid_time' ,'end_time'],
                $aNewScheduleTrips
            )->execute();
        }
    }


    /*
     * Функция удаляет все действующие рейсы (Trip) соответствующие текущему расписанию schedule, кроме тех
     *  на даты которых присутствуют заказы
     */
    public function deleteNotUsedTrips()
    {
        // даты действия расписания это дни от start_date до start_date следующего расписания
        // найдем следующее расписание
        $next_schedule = Schedule::find()
            ->where(['direction_id' => $this->direction_id])
            ->andWhere(['>', 'start_date', $this->start_date])
            ->orderBy(['start_date' => SORT_ASC])
            ->one();

        if($next_schedule == null) { // следующего расписания ограничивающего текущее не существует, значит текущее расписание бесконечно

            // из действующих дат текущего расписания и направления исключаем дни на которые сделаны заказы (с данным направлением)
            $orders = Order::find()
                ->where(['direction_id' => $this->direction_id])
                ->andWhere(['>=', 'date', $this->start_date])
                ->all();
            //echo "orders:<pre>"; print_r($orders); echo "</pre>";
            $aOrdersDate = ArrayHelper::map($orders, 'date', 'date');
            //echo "aOrdersDate:<pre>"; print_r($aOrdersDate); echo "</pre>";

            // по полученным действующим датам текущего расписания и направлению находим действующие рейсы и удаляем их
            $trips = Trip::find()
                ->where(['direction_id' => $this->direction_id])
                ->andWhere(['>=', 'date', $this->start_date])
                ->andWhere(['not in', 'date', $aOrdersDate])
                ->all();
            $aTripsId = ArrayHelper::map($trips, 'id', 'id');
            //echo "aTripsId:<pre>"; print_r($aTripsId); echo "</pre>";
            //echo "trips:<pre>"; print_r($trips); echo "</pre>";
            //exit;

            if(count($aTripsId) > 0) {
                Trip::deleteAll(['id' => $aTripsId]);
            }

        }else {

            // из действующих дат текущего расписания и направления исключаем дни на которые сделаны заказы (с данным направлением)
            $orders = Order::find()
                ->where(['direction_id' => $this->direction_id])
                ->andWhere(['>=', 'date', $this->start_date])
                ->andWhere(['<', 'date', $next_schedule->start_date])
                ->all();
            $aOrdersDate = ArrayHelper::map($orders, 'date', 'date');

            // по полученным действующим датам текущего расписания и направлению находим действующие рейсы и удаляем их
            $trips = Trip::find()
                ->where(['direction_id' => $this->direction_id])
                ->andWhere(['>=', 'date', $this->start_date])
                ->andWhere(['<', 'date', $next_schedule->start_date])
                ->andWhere(['NOT IN', 'date', $aOrdersDate])
                ->all();
            $aTripsId = ArrayHelper::map($trips, 'id', 'id');
            if(count($aTripsId) > 0) {
                Trip::deleteAll(['id' => $aTripsId]);
            }
        }

        return true;
    }
}
