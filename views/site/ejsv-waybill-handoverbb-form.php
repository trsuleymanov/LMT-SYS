<?php

use kartik\date\DatePicker;
use yii\helpers\Html;

?>
<style type="text/css">
    #bb-table {
        width: 200px;
        border-collapse: collapse;
    }
    #bb-table td {
        padding: 3px; /* Поля вокруг содержимого таблицы */
        border: 1px solid black; /* Параметры рамки */
    }
    #password {
        width: 200px;
        display: inline-block;
    }
    #submit-password-with-bb-data {
        display: inline-block;
        margin-left: 5px;
    }
</style>

<input type="hidden" id="waybill-number" value="<?= ($waybill != null ? $waybill->number : 0) ?>" />
<?php /*
<input type="hidden" id="notaccountability-transport-report-id" value="<?= ($notaccountability_transport_report != null ? $notaccountability_transport_report->id : 0) ?>" />
 */ ?>
<input type="hidden" id="day_report_transport_circle-id" value="<?= ($day_report_transport_circle != null ? $day_report_transport_circle->id : '0') ?>" />
<input type="hidden" id="transport-id" value="<?= $transport->id ?>" />
<input type="hidden" id="driver-id" value="<?= $driver->id ?>" />
<input type="hidden" name="role" value="<?= Yii::$app->session->get('role_alias') ?>" />
<?php if($waybill != null) {
    if($waybill->date_of_issue > 0) { ?>
        <input type="hidden" id="date" value="<?= date("d.m.Y", $waybill->date_of_issue) ?>" />
    <?php }
} ?>
<?php if($waybill != null) { ?>

    Сдача выручки по <b>ПЛ #<?= $waybill->number ?> от <?= date('d.m.Y', $waybill->date_of_issue) ?></b><br>
    за <?= $waybill->transport->color ?> <?= $waybill->transport->model ?> <?= $waybill->transport->car_reg ?><br>
    <b>Водитель: </b><?= $waybill->driver->fio ?><br /><br />

    <table id="bb-table">
        <tr><td><b>B1</b>: <?= $waybill->hand_over_b1 ?></td><td><b>B2</b>: <?= $waybill->hand_over_b2 ?></td></tr>
        <tr><td id="exist-b1" is-exist="<?= ($waybill->hand_over_b1 > 0 ? 'true' : 'false') ?>">&nbsp;<?= ($waybill->hand_over_b1_data > 0 ? date('d.m.Y', $waybill->hand_over_b1_data) : '') ?></td><td id="exist-b2" is-exist="<?= ($waybill->hand_over_b1 > 0 ? 'true' : 'false') ?>">&nbsp;<?= ($waybill->hand_over_b2_data > 0 ? date('d.m.Y', $waybill->hand_over_b2_data) : '') ?></td></tr>
    </table>
    <br />
    <?php if($waybill->hand_over_b1 > 0 && $waybill->hand_over_b2 > 0) { ?>
        <p>Невозможно сдать выручку. Обратитесь к администратору</p> <button type="button" id="close-ejsv-waybill-handoverbb-form" class="btn btn-info">Закрыть</button>
    <?php }else { ?>
        <button type="button" id="driver-gives-proceeds" class="btn btn-info">Водитель сдает выручку</button>
    <?php } ?>

<?php }  ?>


<div id="input-bb-group">
    <br />
    <?php /*
    <p id="bb-title">Заполните поля:</p>
    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Дата</label>
            <?php
            echo DatePicker::widget([
                'name' => 'new_date',
                'value' => date('d.m.Y'),
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy',
                ],
                //'disabled' => true,
                'removeButton' => false,
            ]);
            ?>
        </div>
    </div>*/ ?>
    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Водитель сдает сумму:</label>
            <input type="text" id="hand_over_bb" class="form-control" placeholder="Сумма" value="">
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= Html::button('Далее', ['class' => 'btn btn-info', 'id' => 'submit-bb-data']) ?>
        </div>
    </div>
</div>


<div id="input-password" style="display: none;">
    <p>Введите пароль для подтверждения получения денежных средств</p>
    <input id="password" type="password" class="form-control" />
    <?= Html::button('Ок', ['class' => 'btn btn-info', 'id' => 'submit-password-with-bb-data']) ?>
</div>
