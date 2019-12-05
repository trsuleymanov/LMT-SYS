<?php
use app\models\Setting;
?>
<div class="modal-title">Мобильные заявки<button type="button" class="modal-close">×</button></div>
<div class="modal-body">
    <?php

    foreach ($mobile_orders as $order) { ?>
        <div class="clientext<?= (!empty($order->first_opened_form_time) ? ' selected' : '') ?>" order-id="<?= $order->id ?>">
            <?= $order->client != null ? '<span class="call-phone-button" phone="'.$order->client->mobile_phone.'" >'.Setting::changeShowingPhone($order->client->mobile_phone, 'show_short_clients_phones').'</span>' : 'тел - неформат' ?>
            &nbsp;&nbsp;<?= $order->client_name ?>
            &nbsp;&nbsp;<?= date('d.m.Y', $order->date)?>
            &nbsp;&nbsp;<?= $order->trip != null ? $order->trip->mid_time : 'время - неформат' ?>
            &nbsp;&nbsp;<button>Обработать</button>
        </div>
    <?php } ?>
</div>
