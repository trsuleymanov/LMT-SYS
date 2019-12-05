<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\models\OrderCancellationReason;

Pjax::begin([
    'enablePushState' => false, // отключаем обновление урла в браузере
]);

$columns = \app\models\DayReportTransportCircleSearch::getGridColumns($dataProvider, $date);

echo GridView::widget([
    'id' => 'trip-transports-grid',
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    //'filterModel' => $searchModel,
    'columns' => $columns
]);


Pjax::end();
?>
<?php
/*
$order_cancellation_reasons = OrderCancellationReason::find()->all();
if(!in_array(Yii::$app->session->get('role_alias'), ['editor', 'manager'])) {
    $num = 1;
    foreach ($order_cancellation_reasons as $order_cancellation_reason) {
        echo $num . '. <a href="' . Url::to(['/order/cancellation-reason-orders', 'date' => $date, 'cancellation_reason_id' => $order_cancellation_reason->id]) . '" target="_blank">Заказы, удаленные со статусом "' . $order_cancellation_reason->name . '"</a>;<br />';
        $num++;
    }
}*/
?>
<?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
    <a href="<?= Url::to(['/order/cancellation-reason-orders', 'date' => $date]) ?> " target="_blank">Удаленные заказы</a><br />
<?php } ?>
<br />