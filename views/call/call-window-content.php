<?php
use yii\helpers\ArrayHelper;
use app\models\OrderStatus;
use app\models\Setting;

$aOrderStatuses = ArrayHelper::map(OrderStatus::find()->all(), 'id', 'code');
$canceled_order_status_id = array_flip($aOrderStatuses)['canceled'];

$user = \Yii::$app->user->identity;
if($user != null) {
    $user_role_alias = $user->userRole->alias;
}else {
    $user_role_alias = null;
}


?>
<div id="calls-window" call-id="<?= $call_id ?>">
    <?php
    // окно звонка - удали меня когда-то потом...
    //        echo $this->render('@app/views/client-event/call_data_form', [
    //            'client_phone' => $client_phone,
    //            'call_id' => $call_id
    //        ]);

    // роли могли потеряться/не быть в сессии, поэтому если это так то перепишу роли в сессии.
    if(empty(Yii::$app->session->get('role_alias'))) {
        Yii::$app->session->set('role_alias', $user->userRole->alias);
        Yii::$app->session->set('role_id', $user->userRole->id);
    }
    ?>
    <?php if($call_id > 0) { ?>
        <div class="call-block" call-id="<?= $call_id ?>" client-phone="<?= $client_phone ?>">
            <div class="subscriber-block"><b>Звонок с номера:</b> «<?= Setting::changeShowingPhone($client_phone, 'show_short_clients_phones') ?>»</div>
            <div><b>Длительность разговора:</b> <span class="time" active="<?= ($start_speaking == true ? 'true' : 'false') ?>"><?= ($call_speaking_seconds < 10 ? '0' : '').$call_speaking_seconds ?> сек</span></div>
        </div>

        <?php if($client == null) { ?>
            <div><b>ФИО пассажира:<b> <red>Новый клиент<red>, <b>рейтинг отказов:</b> NA</div>
        <?php }else { ?>
            <div><b>ФИО пассажира:</b> <a href="/client/view?id=<?= $client->id ?>" target="_blank"><?= $client->name ?></a>, <b>рейтинг отказов:</b> <?= $client->getCurrentYear1hRejection() ?>/<?= $client->getCurrentYear12hRejection() ?>-<?= $client->getPastYears1hRejection() ?>/<?= $client->getPastYears12hRejection() ?></div>
        <?php } ?>

    <?php }else { ?>
        <?php if($client == null) { ?>
            <div><b>ФИО пассажира:<b> <red>Новый клиент<red>(<?= $client_phone ?>), <b>рейтинг отказов:</b> NA</div>
        <?php }else { ?>
            <div><b>ФИО пассажира:</b> <a href="/client/view?id=<?= $client->id ?>" target="_blank"><?= $client->name ?></a> (<?= $client_phone ?>), <b>рейтинг отказов:</b> <?= $client->getCurrentYear1hRejection() ?>/<?= $client->getCurrentYear12hRejection() ?>-<?= $client->getPastYears1hRejection() ?>/<?= $client->getPastYears12hRejection() ?></div>
        <?php } ?>
    <?php } ?>

</div>
<hr />

<div id="call-client-form">

    <?php /*
        <div id="call-panel">
            <div id="call-panel-left">
                <?php // echo Html::a('Новый заказ', '#', ['id' => 'add-order', 'class' => 'btn btn-success']); ?>
                <?= Html::a('Запись на сегодня', '#', ['id' => 'new-order-today', 'class' => 'btn btn-default pull-left',]); ?>
                <?= Html::a('Запись на завтра', '#', ['id' => 'new-order-tomorrow', 'class' => 'btn btn-default pull-left',]); ?>
                <?= Html::a('Записать на другой день', '#', ['id' => 'new-order-another-day', 'class' => 'btn btn-default pull-left', ]); ?>
            </div>
            <div id="call-panel-right">
                <?php if($client != null) { ?>
                    <?= Html::a('Клиент', '/client/view?id='.$client->id, ['class' => 'btn btn-info pull-left', 'target' => '_blank', 'style' => 'margin-left: 20px;']); ?>
                <?php } ?>
                <?= Html::a('Перевод', '#', ['class' => 'btn btn-info pull-left', 'target' => '_blank', 'style' => 'margin-left: 20px;']); ?>
                <?= Html::a('Закончить вызов', '#', ['id' => '', 'class' => 'btn btn-default pull-right', 'target' => '_blank',  'style' => 'margin-right: 20px;']); ?>
            </div>
        </div>
        */ ?>

    <?php if($orderDataProvider->getTotalCount() > 0) {
        echo \Yii::$app->view->renderFile('@app/views/call/_grid.php', [
            'client' => $client,

            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
            //'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,

            'aOrderStatuses' => $aOrderStatuses,
            'user_role_alias' => $user_role_alias,
            'canceled_order_status_id' => $canceled_order_status_id
        ]);
    }else {
        echo '<p style="margin-left: 20px;">Новых заказов у клиента нет</p>';
    } ?>
</div>

<?php if($searchOrderByPhoneDataProvider->getTotalCount() > 0) { ?>
<br /><br />
<div id="dop-phones-clients-form">
    <div style="color: red; margin-left: 20px;">Проверьте дубликаты:</div>
    <?php
    // окно данных клиента с заказами
    echo \Yii::$app->view->renderFile('@app/views/call/_grid.php', [
        'client' => $client,

        'orderSearchModel' => $orderSearchModel,
        'orderDataProvider' => $searchOrderByPhoneDataProvider,
        //'searchOrderByPhoneDataProvider' => $searchOrderByPhoneDataProvider,

        'aOrderStatuses' => $aOrderStatuses,
        'user_role_alias' => $user_role_alias,
        'canceled_order_status_id' => $canceled_order_status_id
    ]);
    ?>
    <?php
    } ?>
</div>