<?php
use app\components\Helper;
use app\assets\IncomingOrdersWidgetAsset;
use app\models\Call;
use app\models\CallCase;

IncomingOrdersWidgetAsset::register($this);

//$missed_calls_count = Call::find()
//    ->where(['call_direction' => 'input'])
//    ->andWhere(['status' => 'not_completed'])
//    ->andWhere(['<', 'ats_eok_time', time()])
//    ->andWhere(['>', 'ats_eok_time', 0])
//    ->count();

$missed_cases_count = CallCase::find()
    ->where(['case_type' => 'missed'])
    ->andWhere(['status' => 'not_completed'])
    ->count();
//$missed_cases_count = 23;

//$incoming_calls_count = Call::find()->where(['t_hungup' => NULL])->andWhere(['t_answer' => NULL])->count();
$incoming_calls_count = Call::find()
    ->where(['t_answer' => NULL])
    ->andWhere(['call_direction' => 'input'])
    ->andWhere([
        'OR',
        ['ats_eok_time' => 0],
        ['ats_eok_time' => NULL],
    ])
    ->count();
//$incoming_calls_count = 10;

// проверяем наличия текущего звонка у оператора (входящего либо исходящего)
if(Yii::$app->user != null && Yii::$app->user->id > 0) {
    $active_call = Call::find()
        ->where(['handling_call_operator_id' => Yii::$app->user->id])
        ->andWhere(['>', 't_answer', 0])
        ->andWhere([
            'OR',
            ['>', 't_hungup', time()],
            ['ats_eok_time' => 0],
            ['ats_eok_time' => NULL],
        ])->one();
}else {
    $active_call = null;
}
//// проверяем наличия текущего звонка у оператора (исходящего)
//if($active_call == null) {
//    $active_call = Call::find()
//        ->where(['call_direction' => 'output'])
//        ->andWhere(['handling_call_operator_id' => Yii::$app->user->id])
//        ->andWhere(['>', 't_answer', 0])
//        ->andWhere([
//            'OR',
//            ['>', 't_hungup', time()],
//            ['ats_eok_time' => 0],
//            ['ats_eok_time' => NULL],
//        ])->one();
//}

//$active_call = Call::find()->one();

?><div id="incoming-orders-widget">
    <div class="incoming-orders-block">
        <div id="directions">
            <div class="direction-1<?= ($direction1_has_bad_trip ? ' red': '')?>"><span class="direction-name">АК</span>/<span class="orders-count"><?= $direction1_trips_count ?></span></div>
            <div class="direction-2<?= ($direction2_has_bad_trip ? ' red': '')?>"><span class="direction-name">КА</span>/<span class="orders-count"><?= $direction2_trips_count ?></span></div>
        </div>
        <?php /*if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
            <div class="incoming-orders-count"><?= count($mobile_orders) ?></div>
        <?php } */?>
    </div>
    <div id="indicator-block">
        <?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
            <div class="incoming-orders-count"><?= (count($client_orders)) ?></div>
        <?php //} ?>
        <div id="missed-calls" <?= ($missed_cases_count > 0 ? 'class="active" ' : '') ?>>
            <img class="arrow-tube-img" src="/img/arrow-tube.png" />
            <div class="missed-calls-count"><?= $missed_cases_count ?></div>
        </div>
        <?php /*if($active_call != null) { ?>
            <div id="is-calling" class="active" client_phone="<?= $active_call->operand ?>" call_id="<?= $active_call->id ?>"></div>
            <div id="incoming-call"><div id="incoming-calls-count"><?= $incoming_calls_count ?></div></div>
        <?php }else { ?>
            <div id="is-calling"></div>
            <div id="incoming-call" <?= ($incoming_calls_count > 0 ? 'class="active" ' : '') ?>><div id="incoming-calls-count"><?= $incoming_calls_count ?></div></div>
        <?php }*/ ?>
        <?php if($incoming_calls_count > 0) { ?>
            <div id="incoming-call" class="active"><div id="incoming-calls-count"><?= $incoming_calls_count ?></div></div>
        <?php }else { ?>
            <div id="incoming-call"><div id="incoming-calls-count">0</div></div>
        <?php } ?>
    </div>
    <?php if($active_call != null) { ?>
        <div id="is-calling" class="but-online active" client_phone="<?= $active_call->operand ?>" call_id="<?= $active_call->id ?>">ON AIR</div>
    <?php }else { ?>
        <div id="is-calling" class="but-online">ON AIR</div>
    <?php } ?>
    <?php /*
    <div class="incoming-calls-block">
        <div id="missed-calls" <?= ($missed_cases_count > 0 ? 'class="active" ' : '') ?>>
            <img class="arrow-tube-img" src="/img/arrow-tube.png" />
            <div class="missed-calls-count"><?= $missed_cases_count ?></div>
            <!--<div style="margin-top: 10px; margin-bottom: 10px; display: inline-block; width: 22px; height: 22px; background: #6DCD38; border-radius: 50%;"></div>-->
        </div>
        <div id="incoming-call" <?= ($incoming_calls_count > 0 ? 'class="active" ' : '') ?>><div id="incoming-calls-count"><?= $incoming_calls_count ?></div></div>
        <?php if($active_call != null) { ?>
            <div id="is-calling" class="active" client_phone="<?= $active_call->operand ?>" call_id="<?= $active_call->id ?>"></div>
        <?php }else { ?>
            <div id="is-calling"></div>
        <?php } ?>
    </div>
    */ ?>
</div>
<div id="active-trips-modal" <?= $trips_modal_is_open ? '' : 'style="display: none;"' ?>>
    <?= $this->render('active-trips-modal', [
        'aActiveTrips' => $aActiveTrips
    ]) ?>
</div>
<?php //if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
<div id="incoming-clientext-orders-modal" <?= ($incoming_clientext_orders_is_open ? '' : 'style="display: none;"')?>>
    <?php
    echo $this->render('incoming-mobile-orders-modal', [
        'mobile_orders' => $mobile_orders
    ]); ?>
</div>
    <div id="incoming-request-orders-modal" <?= ($incoming_request_orders_is_open ? '' : 'style="display: none;"')?>>
        <?= $this->render('incoming-client-orders-modal', [
            'client_orders' => $client_orders,
            //'active_calls' => $active_calls
        ]) ?>
    </div>
<?php //}?>
