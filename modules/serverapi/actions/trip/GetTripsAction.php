<?php

namespace app\modules\serverapi\actions\trip;

use app\models\Client;
use app\models\Trip;
use Yii;
use app\models\DriverLoginForm;
use yii\db\Query;


class GetTripsAction extends \yii\rest\Action
{
    public $modelClass = '';

    /**
     * Возвращается список рейсов за последние 10 дней при условии что среди рейсов есть хотя бы один у которого дата
     * создания/изменения больше чем переданная в скрипт дата.
     *
     * запрос с кодом доступа:
     * curl -i -H "Authorization: SecretKey lsaeu5jERTffd_7" "Accept:application/json" -H "Content-Type:application/json" -XPOST "http://tobus-yii2.ru/serverapi/trip/get-trips?created_updated_at=1532094772&directions_ids=1,2"
     */
    public function run($created_updated_at, $directions_ids, $all_days = false)
    {
        // нужны поля клиента: id, name, mobile_phone, логин - нет такого, пароль - нет такого
        \Yii::$app->response->format = 'json';


        $aDirections = explode(',', $directions_ids);

        if($all_days == false) {

            // даты: [сегодня; сегодня + 10дней]
            $aUnixDays = [];
            //$today_mktime = strtotime(date("d.m.Y"));
            $aUnixDays[] = strtotime(date("d.m.Y")); // today
            for ($i = 1; $i <= 10; $i++) {
                $day_mktime = $aUnixDays[0] + $i * 86400;
                $aUnixDays[$day_mktime] = $day_mktime;
            }


            // если рейсов за даты $aUnixDays не существует, значит их нужно сгенерировать
            $aDirectionsTrips = [];
            $trips = Trip::find()->where(['date' => $aUnixDays])->andWhere(['direction_id' => $aDirections])->all();
            foreach ($trips as $trip) {
                $aDirectionsTrips[$trip->direction_id][$trip->date] = 1;
            }
            // проверяем полный комплект направлений-рейсов
            foreach ($aDirections as $direction_id) {
                foreach ($aUnixDays as $day_mktime) {
                    if (!isset($aDirectionsTrips[$direction_id][$day_mktime])) {
                        Trip::getTrips($day_mktime, $direction_id); // если нет, значит генерируем
                        $has_changed_trip = true;
                    }
                }
            }


            // поиск измененных рейсов, а заодно и поиск самой последней даты создания/изменения рейса
            $trips = Trip::find()
                ->where(['date' => $aUnixDays])
                ->andWhere([
                    'OR',
                    ['>', 'created_at', $created_updated_at],
                    ['>', 'updated_at', $created_updated_at]
                ])
                ->all();

            if(count($trips) > 0 ) {

                $max_date = $created_updated_at;
                foreach($trips as $trip) {
                    if($trip->created_at > $max_date) {
                        $max_date = $trip->created_at;
                    }
                    if($trip->updated_at > $max_date) {
                        $max_date = $trip->updated_at;
                    }
                }

                // возвращаем все рейсы дат $aUnixDays
                if($all_days == false) {
                    $trips = Trip::find()->where(['date' => $aUnixDays])->all();
                }

                return [
                    'new_max_date' => $max_date,
                    'trips' => $trips
                ];

            }else {
                return [
                    'new_max_date' => null,
                ];
            }

        }else { // $all_days = true
            
            $trips = Trip::find()->all();

            $max_date = $created_updated_at;
            foreach($trips as $trip) {
                if($trip->created_at > $max_date) {
                    $max_date = $trip->created_at;
                }
                if($trip->updated_at > $max_date) {
                    $max_date = $trip->updated_at;
                }
            }

            return [
                'new_max_date' => $max_date,
                'trips' => $trips
            ];
        }


    }
}
