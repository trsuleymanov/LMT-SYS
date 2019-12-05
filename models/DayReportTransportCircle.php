<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport_circle".
 *
 * @property integer $id
 * @property integer $transport_id
 * @property string $base_city_trip_id
 * @property integer $notbase_city_trip_id
 * @property integer $state
 */
class DayReportTransportCircle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'day_report_transport_circle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transport_id', 'base_city_trip_id', 'notbase_city_trip_id', 'state', 'base_city_trip_start_time',
                'notbase_city_trip_start_time', 'base_city_day_report_id', 'notbase_city_day_report_id',
                'time_setting_state'
            ], 'integer'],
            [['total_proceeds', 'total_paid_summ'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transport_id' => 'Транспорт',
            'base_city_trip_id' => 'Рейс выезда из города базирования',
            'notbase_city_trip_id' => 'Рейс выезда из промежуточного города',
            'state' => 'Состояние круга',
            'time_setting_state' => 'Время установки статуса',
            'base_city_trip_start_time' => 'Время первой точки рейса города базирования',
            'notbase_city_trip_start_time' => 'Время первой точки рейса промежуточного города',
            'base_city_day_report_id' => 'id отчета дня отправки из города базирования',
            'notbase_city_day_report_id' => 'id отчета дня отправки из промежуточного города',
            'total_proceeds' => 'Финальный расчет',
            'total_paid_summ' => 'Оплачено'
        ];
    }

    public function getBaseCityDayReport()
    {
        return $this->hasOne(DayReportTripTransport::className(), ['id' => 'base_city_day_report_id']);
    }

    public function getNotbaseCityDayReport()
    {
        return $this->hasOne(DayReportTripTransport::className(), ['id' => 'notbase_city_day_report_id']);
    }

    public function getBaseCityTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'base_city_trip_id']);
    }

    public function getNotbaseCityTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'notbase_city_trip_id']);
    }

    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'transport_id']);
    }

    /*
     * Определяется один из 6-ти случаев отображения строки в таблице текущего отчета дня
     */
    public function getEvent($date = '') {
        //Случаи отображения таблицы:
        //
        //0. есть левая и правая за текущий день
        // - все отображается также как и сейчас
        //
        //1. есть левая за текущий день, нет правой вообще и цикл = 0
        //- в правой колонке рейса пишеться красным "В ожидании обратного рейса...", остальные правые колонки - пустые, Итоговая - пустая
        //
        //2. есть левая за текущий день, нет правой вообще и цикл = 1
        //- в правой колонке Рейса пишеться красным "Без загрузки", остальные правые колонки - пустые, Итоговая - заполняется
        //
        //3. есть левая за текущий день, есть правая за другой день и цикл = 1
        //- в правой колонке рейса пишеться красным Рейс, остальные правые колонки - пустые, Итоговая - пустая
        //
        //4. нет левой совсем, есть правая за текущий день и цикл = 1
        //- в левой колонке в рейсе пишем "старт машины", остальные левые колонки пустые. Правая - нормальная и Итоговая - заполняется
        //
        //5. есть левая за другой (предыдущий) день, и есть правая за текущий день и цикл = 1
        //- левые колонки - красные, правые - обычные, Итоговая - заполняется

        //echo "<pre>"; print_r($this); echo "</pre>";

        // base_city_trip_start_time и notbase_city_trip_start_time
        $unix_today = strtotime($date);

        // +0. есть левая и правая за текущий день
        if(
            $this->base_city_trip_start_time >= $unix_today
            && $this->base_city_trip_start_time < $unix_today + 86400
            && $this->notbase_city_trip_start_time >= $unix_today
            && $this->notbase_city_trip_start_time < $unix_today + 86400
        ) {
            // всё отображается в нормальном виде
            return 0;

        // +1. есть левая за текущий день, нет правой вообще и цикл = 0
        }elseif(
            $this->base_city_trip_start_time >= $unix_today
            && $this->base_city_trip_start_time < $unix_today + 86400
            && intval($this->notbase_city_trip_start_time) == 0
            && $this->state == 0
        ) {
            // - в правой колонке рейса пишеться красным "В ожидании обратного рейса...", остальные правые
            // колонки - пустые, Итоговая - пустая
            return 1;

        // +2. есть левая за текущий день, нет правой вообще и цикл = 1 - Круг завершен
        }elseif(
            $this->base_city_trip_start_time >= $unix_today
            && $this->base_city_trip_start_time < $unix_today + 86400
            && intval($this->notbase_city_trip_start_time) == 0
            && $this->state == 1
        ) {
            // в правой колонке Рейса пишеться красным "Без загрузки", остальные правые колонки - пустые,
            // Итоговая - заполняется
            return 2;

        // +3. есть левая за текущий день, есть правая за другой день (будущий) и цикл = 1 - Круг завершен не в текущий день
        }elseif(
            $this->base_city_trip_start_time >= $unix_today
            && $this->base_city_trip_start_time < $unix_today + 86400
            && $this->notbase_city_trip_start_time >= $unix_today + 86400
            && $this->state == 1
        ) {
            // в правой колонке рейса пишеться красным Рейс, остальные правые колонки - пустые,
            // Итоговая - пустая
            return 3;

        // +4. нет левой совсем, есть правая за текущий день и цикл = 1
        }elseif(
            intval($this->base_city_trip_start_time) == 0
            && $this->notbase_city_trip_start_time >= $unix_today
            && $this->notbase_city_trip_start_time < $unix_today + 86400
            && $this->state == 1
        ) {
            // - в левой колонке в рейсе пишем "старт машины", остальные левые колонки пустые.
            // Правая - нормальная и Итоговая - заполняется
            return 4;

        // 5. есть левая за другой (предыдущий) день, и есть правая за текущий день и цикл = 1
        }elseif(
            $this->base_city_trip_start_time < $unix_today
            && $this->notbase_city_trip_start_time >= $unix_today
            && $this->notbase_city_trip_start_time < $unix_today + 86400
            && $this->state == 1
        ) {
            // левые колонки - красные, правые - обычные, Итоговая - заполняется
            return 5;
        }

    }
}
