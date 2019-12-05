<?php
//use yii\helpers\Html;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\TransportRoundCompletingReason;


// 3 - писать общее количество физических мест, общее количество багажа и посылок
// И сумму полученных денежных средств

$trip = $trip_transport->trip;
$direction = $trip->direction;
$transport = $trip_transport->transport;

$bag_count = 0; // сумок
$suitcase_count = 0; // чемоданов
$oversized_count = 0; // негабаритов
$fact_orders = $trip_transport->factOrdersWithoutCanceled;
$price = 0;

foreach($fact_orders as $fact_order) {
    $bag_count += $fact_order->bag_count;
    $suitcase_count += $fact_order->suitcase_count;
    $oversized_count += $fact_order->oversized_count;

    $price += $fact_order->price;
}

?>
<input type="hidden" name="trip-transport-id" value="<?= $trip_transport->id ?>" />
<div class="row">
    <div class="col-md-4">Рейс</div>
    <div class="col-md-6"><?= $direction->sh_name ?> <?= $trip->name ?></div>
</div>
<div class="row">
    <div class="col-md-4">Т/с</div>
    <div class="col-md-6"><?= $transport->model ?> <?= $transport->car_reg ?></div>
</div>
<div class="row">
    <div class="col-md-4">Отправление в</div>
    <div class="col-md-6"><?= date('d.m.Y H:i') ?></div>
</div>
<div class="row">
    <div class="col-md-4">Статистика</div>
    <div class="col-md-6">
        Мест посажено: <?= $trip_transport->factSatPlacesCount; ?><br />
        Cумок: <?= $bag_count ?>, Чемоданов: <?= $suitcase_count ?>, Негабаритов: <?= $oversized_count ?><br />
        Сумма: <?= Yii::$app->formatter->asCurrency($price) ?>
    </div>
</div>

<br />
<?php /*
<div class="row">
    <div class="col-md-4">
        <?= Html::checkbox('round-is-completed', false, ['label' => '']) ?> Круг завершен
    </div>
</div>
*/ ?>
<div id="transport_round_completing_reasons" class="row" style="display: none;">
    <div class="col-md-6">
        <?= Html::dropDownList('transport_round_completing_reason', 0, ArrayHelper::map(TransportRoundCompletingReason::find()->all(), 'id', 'name'), ['class' => 'form-control']) ?>
    </div>
</div>
<br />

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <?= Html::button('Отправить', ['id' => 'send-trip-transport', 'class' => 'btn btn-success button-submit', ]) ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <?= Html::button('Не отправлять', ['class' => 'btn btn-default button-close', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
        </div>
    </div>
</div>