<?php

use yii\db\Migration;
use app\models\Trip;
use yii\helpers\ArrayHelper;

/*
 * В таблице trip поле date содержало дату + время в unixtime формате, теперь должно содержать только дату
 */
class m170619_095651_trip_change_date extends Migration
{
    public function up()
    {
        $aNewDateTrips = [];
        $aTrips = ArrayHelper::map(Trip::find()->all(), 'id', 'date');
        foreach($aTrips as $trip_id => $unixdate) {
            $new_unixdate = strtotime(date('d.m.Y', $unixdate));
            $aNewDateTrips[$new_unixdate][] = $trip_id;
        }

        foreach($aNewDateTrips as $new_unixdate => $aTripsId) {
            $sql = 'UPDATE `trip` SET `date`="'.$new_unixdate.'" WHERE id IN('.implode(',', $aTripsId).')';
            Yii::$app->db->createCommand($sql)->execute();
        }
    }

    public function down()
    {
        $trips = Trip::find()->all();
        foreach($trips as $trip) {
            $new_unixdate = strtotime(date('d.m.Y', $trip->date).' '.$trip->mid_time);
            $sql = 'UPDATE `trip` SET `date`="'.$new_unixdate.'" WHERE id='.$trip->id;
            Yii::$app->db->createCommand($sql)->execute();
        }
    }
}
