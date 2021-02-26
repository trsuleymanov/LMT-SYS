<?php
use app\models\Call;
use app\models\DispatcherAccounting;

//    ID зв.,
//    Напр. (вх./исх.),
//    Номер (+7900-123-4567),
//    Время ответа (06.02.2019 20:55:44),
//    Время заверш. (06.02.2019 20:55:44),
//    Оператор (Имя_польз),
//    Статус (список значений в таблице),
//    Conform (Да/Нет),
//    Событие (см. в таблице)
?>
<table class="dockings-list" case-id="<?= $case->id ?>">

    <tr>
        <td>ID зв.</td>
        <td>Напр.</td>
        <td>Номер</td>
        <td>Дата</td>
        <td>Время ответа</td>
        <td>Время заверш.</td>
        <td>Оператор</td>
        <td>Статус</td>
        <td>Conform</td>
        <td>Событие</td>
    </tr>

    <?php
    foreach($dockings as $docking) {

        $call = $docking->call;
        ?>
        <tr>
            <td><?= $docking->call_id ?></td>
            <td><?= ($call->call_direction == 'input' ? 'вх.' : 'исх.') ?></td>
            <td><?= $call->operand ?></td>
            <td><?= (!empty($call->t_create) ? date('d.m.Y', $call->t_create) : '') ?></td>
            <td><?= (!empty($call->t_answer) ? date('H:i:s', $call->t_answer) : '') ?></td>
            <td><?= (!empty($call->t_hungup) ? date('H:i:s', $call->t_hungup) : '') ?></td>
            <td><?= (!empty($call->handling_call_operator_id) ? $call->handlingCallOperator->username : '') ?></td>
            <td><?= (!empty($call->status) ? Call::getStatuses()[$call->status] : '') ?></td>
            <td><?= ($docking->conformity == true ? 'Да' : 'Нет') ?></td>
            <td><?= (!empty($docking->click_event) ? DispatcherAccounting::getOperationTypes()[$docking->click_event] : '') ?></td>
        </tr>
    <?php } ?>
</table>

