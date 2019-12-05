<?php

use app\models\Client;
use yii\helpers\ArrayHelper;

// +7-111-111-1111 Шарифуллина 22.01.2018 10:00 "Обработать"
if(count($clientexts_orders) > 0) {
?>
    <div id="clientext-list">
        <?php
        /*foreach ($clientexts as $clientext) { ?>
            <div class="clientext <?= (!empty($clientext->start_processing_operator_id) ? 'selected' : '') ?>" clientext-id="<?= $clientext->id ?>"><?= $clientext->client_phone ?>&nbsp;&nbsp;<?= $clientext->client_fio?>&nbsp;&nbsp;<?= date('d.m.Y', $clientext->data_mktime)?>&nbsp;&nbsp;<?= $clientext->time?>&nbsp;&nbsp;<button>Обработать</button></div>
        <?php } */
        foreach ($clientexts_orders as $order) { ?>
            <div class="clientext" order-id="<?= $order->id ?>"><?= $order->client != null ? $order->client->mobile_phone : 'тел - неформат' ?>&nbsp;&nbsp;<?= $order->client_name ?>&nbsp;&nbsp;<?= date('d.m.Y', $order->date)?>&nbsp;&nbsp;<?= $order->trip != null ? $order->trip->mid_time : 'время - неформат' ?>&nbsp;&nbsp;<button>Обработать</button></div>
        <?php } ?>
    </div>
<?php } ?>

