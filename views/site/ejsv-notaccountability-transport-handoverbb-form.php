<?php
use yii\helpers\Html;

?>
<div id="hand-over-bb-block">
    <?= $notaccountability_transport_report->driver->fio ?> сдает сумму: <input type="text" id="hand_over_bb" placeholder="Сумма" value=""> <?= Html::button('Ок', ['class' => 'btn btn-info', 'id' => 'submit-notaccountability-transport-bb-data']) ?>
</div>
<div id="input-password" style="display: none;">
    <p>Введите пароль для подтверждения получения денежных средств</p>
    <input id="password" type="password" />
    <?= Html::button('Ок', ['class' => 'btn btn-info', 'notaccountability-transport-report-id' => $notaccountability_transport_report->id, 'id' => 'submit-password-with-notaccountability-transport-bb-data']) ?>
</div>