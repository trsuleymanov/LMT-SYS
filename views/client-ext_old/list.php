<?php

use app\models\Client;
use yii\helpers\ArrayHelper;

// +7-111-111-1111 Шарифуллина 22.01.2018 10:00 "Обработать"
if(count($clientexts) > 0) {
?>
    <div id="clientext-list">
        <?php
        //$clients = Client::find()->where(['id' => ArrayHelper::map($clientexts, 'client_id', 'client_id')])->all();
        //$aClientsPhone = ArrayHelper::map($clients, 'id', 'mobile_phone');
        foreach ($clientexts as $clientext) { ?>
            <div class="clientext <?= (!empty($clientext->start_processing_operator_id) ? 'selected' : '') ?>" clientext-id="<?= $clientext->id ?>"><?= $clientext->client_phone ?>&nbsp;&nbsp;<?= $clientext->client_fio?>&nbsp;&nbsp;<?= date('d.m.Y', $clientext->data_mktime)?>&nbsp;&nbsp;<?= $clientext->time?>&nbsp;&nbsp;<button>Обработать</button></div>
        <?php } ?>
    </div>
<?php } ?>

