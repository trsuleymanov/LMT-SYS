<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\SelectWidget;
use yii\web\JsExpression;
use app\components\Helper;
?>
<div class="row trip-transport-row" trip-transport-id="<?= $trip_transport->id ?>" sort="<?= $trip_transport->sort ?>">

    <div  class="col-sm-1" style="width: 6%; padding-left: 10px; padding-right: 10px;">
        <button class="btn btn-default trip-transport-sort-up" <?= (isset($num) && $num == 1 ? 'disabled="true" ' : '') ?>><i class="glyphicon glyphicon-arrow-up"></i></button>
    </div>
    <div class="col-sm-5">
        <div class="form-group">
            <?php
//            echo Html::dropDownList(
//                'transport_id[]',
//                $trip_transport->transport_id,
//                $transport_list,
//                [
//                    'class' => 'form-control',
//                    'disabled' => !empty($trip_transport->date_sended)
//                ]
//            );

            echo SelectWidget::widget([
                'model' => $trip_transport,
                'attribute' => 'transport_id',
                'name' => 'transport_id',
                'initValueText' => ($trip_transport->transport_id > 0 && $trip_transport->transport != null ? $trip_transport->transport->car_reg_places_count : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                ],
                'ajax' => [
                    'url' => '/trip-transport/ajax-get-transports-names?trip_id='.$trip->id,
                    'data' => new JsExpression('function(params) {

                        var transports_ids = [];
                        $("#add-cars-form .trip-transport-row").each(function() {
                            var transport_id = $(this).find(\'*[name="TripTransport[transport_id]"]\').val();
                            if(transport_id != undefined) {
                                transports_ids.push(transport_id);
                            }else {
                                alert("transport_id="+transport_id);
                            }
                        });

                        return {
                            search: params.search,
                            selected_transports_ids: transports_ids
                        };
                    }'),
                ],
                'using_delete_button' => false
            ]);
            ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <?php
//            echo Html::dropDownList(
//                'driver_id[]',
//                $trip_transport->driver_id,
//                $driver_list,
//                [
//                    'class' => 'form-control',
//                    'disabled' => !empty($trip_transport->date_sended)
//                ]
//            );
            echo SelectWidget::widget([
                'model' => $trip_transport,
                'attribute' => 'driver_id',
                'name' => 'driver_id',
                'initValueText' => ($trip_transport->driver_id > 0 && $trip_transport->driver != null ? $trip_transport->driver->fio : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                ],
                'ajax' => [
                    'url' => '/trip-transport/ajax-get-drivers-names?trip_id='.$trip->id,
                    'data' => new JsExpression('function(params, obj) {

                        var drivers_ids = [];
                        $("#add-cars-form .trip-transport-row").each(function() {
                            var driver_id = $(this).find(\'*[name="TripTransport[driver_id]"]\').val();
                            if(driver_id.length > 0) {
                                drivers_ids.push(driver_id);
                            }
                        });

                        var selected_transport_id = obj.parents(".trip-transport-row").find(\'*[name="TripTransport[transport_id]"]\').val();

                        return {
                            search: params.search,
                            selected_drivers_ids: drivers_ids,
                            selected_transport_id: selected_transport_id
                        };
                    }'),
                ],
                'using_delete_button' => false
            ]);
            ?>
        </div>
    </div>
    
    <div class="col-sm-1">
        <?php
        if(empty($trip_transport->date_sended)) {
            echo Html::a(
                '<i class="glyphicon glyphicon-trash"></i>',
                Url::to(['/admin/point/ajax-update', 'id' => $trip_transport->id]),
                ['data-original-title' => 'Удалить', 'class' => "btn btn-danger delete-trip-transport"]
            );
        }
        ?>
    </div>
    
    <input type="hidden" name="tt_id[]" value="<?= $trip_transport->id ?>">
    <input type="hidden" name="confirmed[]" value="<?= intval($trip_transport->confirmed) ?>">
</div>
