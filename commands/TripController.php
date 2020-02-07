<?php

namespace app\commands;

use app\models\Order;
use app\models\OrderStatus;
use app\models\Setting;
use app\models\Trip;
use app\models\TripTransport;
use yii\base\ErrorException;
use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;


class TripController extends Controller
{
    // закрытие рейсов
    // команда: php yii trip/close-trips
    public function actionCloseTrips()
    {
        $setting = Setting::find()->one();
        if($setting == null) {
            throw new ErrorException('Настройки не найдены');
        }

        $today_unixdate = strtotime(date('d.m.Y'));
        $today_trips = Trip::find()
            ->where(['date' => $today_unixdate])
            ->andWhere(['date_sended' => NULL])
            ->andWhere(['>', 'date_issued_by_operator', 0])
            ->all();
        if(count($today_trips) > 0) {
            foreach ($today_trips as $trip) {
                $aEndTime = explode(':', $trip->end_time);
                $end_time_mins = 3600*intval($aEndTime[0]) + 60*intval($aEndTime[1]);
                $trip_time_close = $trip->date + $end_time_mins + 60*intval($setting->interval_to_close_trip);
                if(time() > $trip_time_close) {
                    if($trip->send()) {
                        echo "закрыт рейс ".($trip->direction_id == 1 ? 'АК' : 'КА').' '.$trip->name.' (id='.$trip->id.")\n";
                    }
                }
            }
        }

        echo "скрипт отработал\n";
    }


    /*
     * Генерируются рейсы на 30 дней вперед
     * команда: php yii trip/generate-trips
     */
    public function actionGenerateTrips() {

        $aDirections = [1, 2];

        $today_unixtime = strtotime(date('d.m.Y', time()));
        for($i = 0; $i < 31; $i++) {
            $data_unixtime = $today_unixtime + $i*86400;

            foreach ($aDirections as $direction_id) {
                $trip = Trip::find()
                    ->where(['direction_id' => $direction_id])
                    ->andWhere(['date' => $data_unixtime])
                    ->one();
                if($trip == null) {
                    Trip::createStandartTripList($data_unixtime, $direction_id);
                }
            }
        }

        echo "скрипт отработал\n";
    }
}
