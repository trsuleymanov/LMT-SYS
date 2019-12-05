<?php
use app\models\Call;

?>
<table class="calls-block" contact-id="<?= $contact->id ?>">
<?php
    // Здесь нужны колонки:
    //  - направление звонка
    //  - оператор
    //  - время когда был начат звонок
    //  - время начала разговора
    //  - время окончания разговора
    //  - состояние(статус) звонка
?>
    <tr>
        <td>Направление звонка</td>
        <td>Оператор создавший/принявший звонок</td>
        <td>Начало звонка</td>
        <td>Начало разговора</td>
        <td>Окончание</td>
        <td>Состояние</td>
    </tr>

    <?php
    foreach($calls as $call) {
        ?>
        <tr>
            <td><?= ($call->call_from_operator == 1 ? 'исходящий': 'входящий') ?></td>
            <td><?= (!empty($call->handling_call_operator_id) && $call->handlingCallOperator != null ? $call->handlingCallOperator->username : '' ) ?></td>
            <td><?= date('H:i:s', $call->created_at) ?></td>
            <td><?= (!empty($call->answered_at) ? date('H:i:s', $call->answered_at) : '') ?></td>
            <td><?= (!empty($call->finished_at) ? date('H:i:s', $call->finished_at) : '') ?></td>
            <td><?= (!empty($call->status) ? Call::getStatuses()[$call->status] : '') ?></td>
        </tr>
    <?php } ?>
</table>

