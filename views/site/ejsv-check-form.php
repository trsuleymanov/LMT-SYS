<?php


// 5) если оператор выбирает верно, то отображаем табличку 2х2:
// в верхней строчке а левой ячейке - В1, в правой - В2. Под ними - дата сдачи В1 и дата сдачи В2.
?>
<br />
<b style="color: red;">Проверьте данные:</b><br /><br />
<b>ПЛ:</b> #<?= $pl_number ?> от <?= $pl_date ?><br />
<b>Т/с:</b> <?= $transport->color ?> <?= $transport->model ?> <?= $transport->car_reg ?><br />
<b>Водитель:</b> <?= $driver->fio ?><br /><br />

<b>Рейсы круга:</b>
<?php if($trip_from != null) { ?>
    <?= ($trip_from->direction_id == 1 ? 'АК' : 'КА') ?> <?= $trip_from->name ?> ->
<?php }else { ?>
    Нет ->
<?php } ?>
<?php if($trip_to != null) { ?>
    <?= ($trip_to->direction_id == 1 ? 'АК' : 'КА') ?> <?= $trip_to->name ?>
<?php }else { ?>
    Нет
<?php } ?>
<br /><br />

<button type="button" id="accept-check-form" class="btn btn-info">Верно ?</button>
