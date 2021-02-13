<?php

namespace app\commands;

use app\components\Helper;
use app\models\DayReportTransportCircle;
use app\models\DayReportTripTransport;
use app\models\Trip;
use app\models\TripTransport;
use app\models\User;
use Yii;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Консольные команды для работы с отчетом дня
 */
class DayReportController extends Controller
{
    /*
     * Наполнение таблицы отчета дня: day_report_transport_circle, day_report_trip_transport
     *  на основе множества других таблиц
     *
     * команда: php yii day-report/fill-day-reports
     */
    public function actionFillDayReports()
    {
        $start_date = "01.01.2018";
        //$start_date = "18.01.2018";
        //$start_date = "17.01.2018";
        $end_date = date('d.m.Y'); // сегодня (как перелив данных дойдет до совпадения, то будет остановка)
        //$end_date = "18.01.2018";
        $mktime_start_date = strtotime($start_date);
        $mktime_end_date = strtotime($end_date);


        // вначале целиком очищается таблица DayReportTransportCircle

        // !!! это возможно нужно будет переписать чтобы не удалять всю таблицу, или
        // отбекапить вначале таблицу
        $sql = 'TRUNCATE '.DayReportTransportCircle::tableName();
        Yii::$app->db->createCommand($sql)->execute();


        for($mktime_day = $mktime_start_date; $mktime_day <= $mktime_end_date; $mktime_day += 86400)
        {
            // Перебор отправленных машин
            // на каждую отправленную машину произвожу "отправку машины"
            //  - если машина уже была записана, то пропускается запись
//            $sended_trip_transports =
//                TripTransport::find()
//                    ->leftJoin('trip', '`trip`.`id` = `trip_transport`.`trip_id`')
//                ->where(['status_id' => 1])
//                ->andWhere(['`trip`.date' => $mktime_day])
//                ->orderBy(['`trip`.id' => SORT_ASC, '`trip`.start_time' => SORT_ASC])
//                ->all();

            $sended_trip_transports =
                TripTransport::find()
                    ->leftJoin('trip', '`trip`.`id` = `trip_transport`.`trip_id`')
                    ->where(['status_id' => 1])->andWhere(['`trip`.date' => $mktime_day])
                    ->orderBy(['`trip`.start_time' => SORT_ASC])
                    ->limit(30)
                    ->all();


            echo "date=".date('d.m.Y', $mktime_day)." count=".count($sended_trip_transports)."\n";

//            foreach($sended_trip_transports as $sended_trip_transport) {
//                echo $sended_trip_transport->trip->direction->sh_name.' '.$sended_trip_transport->trip->start_time
//                    ." trip_transport_id=".$sended_trip_transport->id."\n";
//            }


            foreach($sended_trip_transports as $sended_trip_transport) {
                //echo "trip_id=".$sended_trip_transport->trip_id." trip_date_sended=".date('d.m.Y H:i', $sended_trip_transport->date_sended)."\n";

                $fact_orders = $sended_trip_transport->factOrdersWithoutCanceled;

                $trip = $sended_trip_transport->trip;
                $direction = $trip->direction;
                $transport = $sended_trip_transport->transport;
                $driver = $sended_trip_transport->driver;
                $current_user = User::find()->where($sended_trip_transport->sender_id)->one();

                // проверяю нет ли такого trip_transport уже в таблице $day_report_trip_transport
                $day_report_trip_transport = DayReportTripTransport::find()
                    ->where(['trip_transport_id' => $sended_trip_transport->id])
                    ->one();


                if($day_report_trip_transport == null) {

                    echo "не найден day_report_trip_transport (с sended_trip_transport_id=".$sended_trip_transport->id."). Создаем запись \n";
                    //exit;
                    //continue;

                    // на самом деле в данном случае стоило бы создать day_report_trip_transport, потому что
                    // на каждую отправленную машину должна быть запись в day_report_trip_transport
                    // ...



                    $day_report_trip_transport = new DayReportTripTransport();
                    $day_report_trip_transport->date = $trip->date;
                    $day_report_trip_transport->direction_id = $direction->id;
                    $day_report_trip_transport->direction_name = $direction->sh_name;
                    $day_report_trip_transport->trip_id = $trip->id;
                    $day_report_trip_transport->trip_name = $trip->name;
                    $day_report_trip_transport->trip_transport_id = $sended_trip_transport->id;
                    $day_report_trip_transport->transport_id = $transport->id;
                    $day_report_trip_transport->transport_car_reg = $transport->car_reg;
                    $day_report_trip_transport->transport_model = $transport->model;
                    $day_report_trip_transport->transport_places_count = $transport->places_count;
                    $day_report_trip_transport->transport_date_sended = $sended_trip_transport->date_sended;
                    $day_report_trip_transport->transport_sender_id = $current_user->id;
                    $day_report_trip_transport->transport_sender_fio = $current_user->lastname.' '.$current_user->firstname;

                    $day_report_trip_transport->driver_id = $driver->id;
                    $day_report_trip_transport->driver_fio = $driver->fio;

                    $day_report_trip_transport->places_count_sent = 0;
                    $day_report_trip_transport->child_count_sent = 0;
                    $day_report_trip_transport->student_count_sent = 0;
                    $day_report_trip_transport->prize_trip_count_sent = 0;
                    $day_report_trip_transport->bag_count_sent = 0;
                    $day_report_trip_transport->suitcase_count_sent = 0;
                    $day_report_trip_transport->oversized_count_sent = 0;
                    $day_report_trip_transport->is_not_places_count_sent = 0;
                    $day_report_trip_transport->proceeds = 0;
                    $day_report_trip_transport->paid_summ = 0;
                    $day_report_trip_transport->airport_count_sent = 0;
                    $day_report_trip_transport->fix_price_count_sent = 0;

                    foreach($fact_orders as $fact_order) {
                        $day_report_trip_transport->places_count_sent += $fact_order->places_count;
                        $day_report_trip_transport->child_count_sent += $fact_order->child_count;
                        $day_report_trip_transport->student_count_sent += $fact_order->student_count;
                        $day_report_trip_transport->prize_trip_count_sent += $fact_order->prize_trip_count;
                        $day_report_trip_transport->bag_count_sent += $fact_order->bag_count;
                        $day_report_trip_transport->suitcase_count_sent += $fact_order->suitcase_count;
                        $day_report_trip_transport->oversized_count_sent += $fact_order->oversized_count;
                        $day_report_trip_transport->is_not_places_count_sent += $fact_order->is_not_places;
                        $day_report_trip_transport->proceeds += $fact_order->price;
                        $day_report_trip_transport->paid_summ += $fact_order->paid_summ;

//                        $pointTo = $fact_order->pointTo;
//                        $pointFrom = $fact_order->pointFrom;
                        $yandexPointTo = $fact_order->yandexPointTo;
                        $yandexPointFrom = $fact_order->yandexPointFrom;
                        if (
                            ($yandexPointTo != null && $yandexPointTo->alias == 'unified')
                            || ($yandexPointFrom != null && $yandexPointFrom->alias == 'unified')
                        ) { // едут в аэропорт или из аэропорта
                            $day_report_trip_transport->airport_count_sent++;
                        }

                        if ($fact_order->use_fix_price == 1) {
                            $day_report_trip_transport->fix_price_count_sent++;
                        }
                    }

                    if (!$day_report_trip_transport->save(false)) {
                        throw new ErrorException('Не удалось сохранить day_report_trip_transport');
                    }


                }else {

                    if (count($fact_orders) > 0) {

                        $day_report_trip_transport->airport_count_sent = 0;
                        $day_report_trip_transport->fix_price_count_sent = 0;
                        foreach ($fact_orders as $fact_order) {

//                            $pointTo = $fact_order->pointTo;
//                            $pointFrom = $fact_order->pointFrom;
                            $yandexPointTo = $fact_order->yandexPointTo;
                            $yandexPointFrom = $fact_order->yandexPointFrom;
                            if (
                                ($yandexPointTo != null && $yandexPointTo->alias == 'unified')
                                || ($yandexPointFrom != null && $yandexPointFrom->alias == 'unified')
                            ) { // едут в аэропорт или из аэропорта
                                $day_report_trip_transport->airport_count_sent++;
                            }

                            if ($fact_order->use_fix_price == 1) {
                                $day_report_trip_transport->fix_price_count_sent++;
                            }
                        }

                        if (!$day_report_trip_transport->save(false)) {
                            throw new ErrorException('Не удалось сохранить day_report_trip_transport');
                        }
                    }
                }


                $trip_start_time = $trip->date + Helper::convertHoursMinutesToSeconds($trip->start_time);
                $transport_circle = DayReportTransportCircle::find()
                    ->where(['transport_id' => $sended_trip_transport->transport->id, 'state' => 0])
                    ->andWhere(['<', 'base_city_trip_start_time', $trip_start_time])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                // если отправляемая машина выезжает из города базирования, то создаем новый круг.
                if($trip->direction->city_from == $sended_trip_transport->transport->base_city_id) {

                    // если ранее с этой машиной уже был круг и он не закрыт, то закрываем его
                    $old_transport_circle = $transport_circle;
                    if($old_transport_circle != null) {
                        $old_transport_circle->state = 1;
                        $old_transport_circle->time_setting_state = time();
                        if(!$transport_circle->save()) {
                            throw new ForbiddenHttpException('Не удалось закрыть старый круг машины');
                        }
                    }

                    $transport_circle = new DayReportTransportCircle();
                    $transport_circle->transport_id = $sended_trip_transport->transport->id;
                    $transport_circle->base_city_trip_id = $trip->id;
                    $transport_circle->base_city_trip_start_time = $trip_start_time;
                    $transport_circle->base_city_day_report_id = $day_report_trip_transport->id;
                    $transport_circle->state = 0;
                    $transport_circle->time_setting_state = time();
                    $transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
                    $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;

                }else { // иначе завершаем старый круг (или создаем новый круг с завершением)

                    if($transport_circle == null) {
                        $transport_circle = new DayReportTransportCircle();
                        $transport_circle->transport_id = $sended_trip_transport->transport->id;
                        $transport_circle->notbase_city_trip_id = $trip->id;
                        $transport_circle->notbase_city_trip_start_time = $trip_start_time;
                        $transport_circle->notbase_city_day_report_id = $day_report_trip_transport->id;
                        $transport_circle->state = 1;
                        $transport_circle->time_setting_state = time();
                        $transport_circle->total_proceeds = $day_report_trip_transport->proceeds;
                        $transport_circle->total_paid_summ = $day_report_trip_transport->paid_summ;
                    }else {
                        $transport_circle->transport_id = $sended_trip_transport->transport->id;
                        $transport_circle->notbase_city_trip_id = $trip->id;
                        $transport_circle->notbase_city_trip_start_time = $trip_start_time;
                        $transport_circle->notbase_city_day_report_id = $day_report_trip_transport->id;
                        $transport_circle->state = 1;
                        $transport_circle->time_setting_state = time();
                        $transport_circle->total_proceeds += $day_report_trip_transport->proceeds;
                        $transport_circle->total_paid_summ += $day_report_trip_transport->paid_summ;
                    }
                }

                if(!$transport_circle->save()) {
                    throw new ForbiddenHttpException('Не удалось сохранить запись машины в таблице кругов');
                }

            }

        }


    }
}