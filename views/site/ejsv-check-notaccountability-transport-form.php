<?php


// 5) если оператор выбирает верно, то отображаем табличку 2х2:
// в верхней строчке а левой ячейке - В1, в правой - В2. Под ними - дата сдачи В1 и дата сдачи В2.

$baseCityTrip = $circle->baseCityTrip;
$notbaseCityTrip = $circle->notbaseCityTrip;

$value = '';
if($circle->base_city_trip_start_time > 0) {
    $value .= date('d.m.Y', $circle->base_city_trip_start_time).' ';
}
if($baseCityTrip != null) {
    $value .= ($baseCityTrip->direction_id == 1 ? 'АК' : 'КА').' '.$baseCityTrip->name.' - ';
}else {
    $value .= 'нет - ';
}

if($circle->notbase_city_trip_start_time > 0) {
    $value .= date('d.m.Y', $circle->notbase_city_trip_start_time).' ';
}
if($notbaseCityTrip != null) {
    $value .= ($notbaseCityTrip->direction_id == 1 ? 'АК' : 'КА').' '.$notbaseCityTrip->name;
}else {
    $value .= 'нет';
}
?>
<br />
<p>Проценты сдает:</p>

<?= $driver->fio ?> за т/с <?= $transport->sh_model ?> <?= $transport->car_reg ?>
<br /><br />

За круг:<br />
<?= $value ?>

<br /><br />
<button type="button" id="accept-check-notaccountability-transport-form" class="btn btn-info">Верно ?</button>