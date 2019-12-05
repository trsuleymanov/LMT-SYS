<?php
use yii\helpers\Html;

?>

<div class="row">
    <div class="col-v-34 form-group-sm">
        <label>Укажите начало круга</label><br />
        <?php

//        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date_of_issue)) {
//            $model->date_of_issue = strtotime($model->date_of_issue);
//        }

//            echo $form
//                    ->field($model, 'trip_transport_start', ['errorOptions' => ['style' => 'display:none;']])
//                    ->dropDownList([0 => 'Нет'] + $aStartTripsNames, [])
//                    ->label('Выбрать начало');

            echo Html::dropDownList('trip_transport_start', $trip_transport_start, [0 => 'Нет'] + $aStartTripsNames);
        ?>
    </div>

    <div class="col-v-2">&nbsp;</div>

    <div class="col-v-44 form-group-sm">
        <label>Укажите окончание круга</label><br />
        <?php
//        echo $form
//                ->field($model, 'trip_transport_end', ['errorOptions' => ['style' => 'display:none;']])
//                ->dropDownList([0 => 'Нет'] + $aEndTripsNames, [])
//                ->label('Выбрать окончание');
        echo Html::dropDownList('trip_transport_end', $trip_transport_end, [0 => 'Нет'] + $aEndTripsNames);
        ?>
    </div>
    <div class="col-v-17 form-group-sm">
        <br />
        <button id="btn-selected-day-report-transport-circle" type="button" class="btn btn-default">Далее</button>
    </div>
</div>
