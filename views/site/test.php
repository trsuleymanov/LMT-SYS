<?php
/*
 * Тест
 */

$this->registerJsFile('js/site/test.js', ['depends'=>'app\assets\AppAsset']);


use app\models\core\QueryWithSave;
use app\models\Trip;
use app\models\TripTransport;
use yii\db\Query;


//$trip = (new QueryWithSave())
//    ->from(Trip::tableName())
//    ->where(['direction_id' => 1])
//    ->andWhere(['date' => 1517000400])
//    ->orderBy(['end_time' => SORT_ASC])
//    ->one();
//echo "trip: <pre>"; print_r($trip); echo "</pre>";
//
//$trip2 = (new QueryWithSave())
//    ->from(Trip::tableName())
//    ->where(['direction_id' => 1])
//    ->andWhere(['date' => 1517000400])
//    ->orderBy(['end_time' => SORT_ASC])
//    ->one();
//echo "trip2: <pre>"; print_r($trip2); echo "</pre>";

//$xz = (new Query())
//    ->from(Trip::tableName())
//    ->innerJoin(TripTransport::tableName(), TripTransport::tableName().'.trip_id='.Trip::tableName().'.id')
//    ->where([Trip::tableName().'.direction_id' => 1])
//    ->andWhere([Trip::tableName().'.date' => strtotime(date('d.m.Y'))])
//    ->orderBy([Trip::tableName().'.end_time' => SORT_ASC]);
//
//$sql = $xz->createCommand()->getRawSql();
//echo 'sql='.$sql.'<br />';
//
//echo "xz:<pre>"; print_r($xz); echo "</pre>";

/*
$trip = (new Query())
    ->from(Trip::tableName())
    ->where(['direction_id' => 1])
    ->andWhere(['date' => 1517000400])
    ->orderBy(['end_time' => SORT_ASC])
    ->one();
echo "trip: <pre>"; print_r($trip); echo "</pre>";

$trip2 = (new Query())
    ->from(Trip::tableName())
    ->where(['direction_id' => 1])
    ->andWhere(['date' => 1517000400])
    ->orderBy(['end_time' => SORT_ASC])
    ->one();
echo "trip2: <pre>"; print_r($trip2); echo "</pre>";

<input id="request-send" type="button" value="Отправить запрос" />
*/
?>


