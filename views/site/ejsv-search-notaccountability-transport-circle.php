<?php
use yii\helpers\Html;

//  {дата начала круга} {рейс с указанием направления} - {дата окончания круга, выбранная выше} {рейс с указанием направления}
?>
<div class="row">
    <div class="col-sm-12 form-group form-group-sm">
        <?php
        if(count($day_report_transport_circles) > 0) {
            foreach($day_report_transport_circles as $circle) {
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
                    //$value .= date('d.m.Y', $circle->notbase_city_trip_start_time).' ';
                    if(date('d.m.Y', $date_end) != date('d.m.Y', $circle->notbase_city_trip_start_time)) {
                        $value .= '<span style="color:red;">'.date('d.m.Y', $circle->notbase_city_trip_start_time).'</span> ';
                    }else {
                        $value .= date('d.m.Y', $circle->notbase_city_trip_start_time).' ';
                    }
                }
                if($notbaseCityTrip != null) {
                    $value .= ($notbaseCityTrip->direction_id == 1 ? 'АК' : 'КА').' '.$notbaseCityTrip->name;
                }else {
                    $value .= 'нет';
                }
                ?>
                <input type="radio" name="notaccountability-transport-circle" value="<?= $circle->id ?>" /> <?= $value ?> <br />
            <?php }
        }else { ?>
            <span style="color: red;">Не найдены круги рейсов данной т/с начатые <?= date('d.m.Y', $date_start) ?></span>
        <?php } ?>
    </div>
</div>

