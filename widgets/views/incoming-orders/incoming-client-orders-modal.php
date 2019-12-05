<?php
use app\models\Call;
use app\models\Client;
use app\models\Setting;
use yii\helpers\ArrayHelper;

$aClientsId = [];
foreach($client_orders as $order) {
    if(!empty($order->client_id)) {
        $aClientsId[$order->client_id] = $order->client_id;
    }
}

$aClientsIdPhone = [];
if(count($aClientsId) > 0) {
    $clients = Client::find()->where(['id' => $aClientsId])->all();
    $phones = ArrayHelper::map($clients, 'mobile_phone', 'mobile_phone');

    $active_calls = Call::find()
        ->where(['operand' => $phones])
        ->andWhere([
            'OR',
            ['>', 't_hungup', time()],
            ['ats_eok_time' => 0],
            ['ats_eok_time' => NULL],
        ])->all();

    if(count($active_calls) > 0) {
        $aActiveCalls = ArrayHelper::index($active_calls, 'operand');
        foreach($clients as $client) {
            if(isset($aActiveCalls[$client->mobile_phone])) {
                $aClientsIdPhone[$client->id] = $client->mobile_phone;
            }
        }
    }
}
?>
<div class="modal-title">Электронные заявки<button type="button" class="modal-close">×</button></div>
<div class="modal-body">
    <?php
    $aDirections = [
        1 => 'АК',
        2 => 'КА'
    ];


    //$aOperandActiveCalls = ArrayHelper::index($active_calls, 'operand');

    // № п/п + дата, время + направление (АК,КА) + Тел. + Время заказа
    // + Поле с кнопкой Обработать. Сортировать ее нужно по полю "Дата, время"
    $i = 1;
    foreach ($client_orders as $order) {  ?>
        <div class="request <?= (isset($aClientsIdPhone[$order->client_id]) ? 'selected' : '') ?>" order-id="<?= $order->id ?>">
            <?= $i ?>
            &nbsp;&nbsp;<?= date("d.m.Y", $order->date) ?>
            &nbsp;&nbsp;<?= (isset($aDirections[$order->direction_id]) ? $aDirections[$order->direction_id] : '') ?>
            &nbsp;&nbsp;<?= $order->client != null ? Setting::changeShowingPhone($order->client->mobile_phone, 'show_short_clients_phones') : '' ?>
            &nbsp;&nbsp;<?= date('d.m.Y H:i', $order->external_created_at) ?>
            &nbsp;&nbsp;<button class="call-phone-button" phone="<?= $order->client->mobile_phone ?>">Обработать</button>
        </div>
    <?php
        $i++;
    } ?>
</div>