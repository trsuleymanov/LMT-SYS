<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\SelectWidget;
use yii\web\JsExpression;
use app\components\Helper;
?>
<div class="row trip-transport-row" onDate="<?=$onDate?>">
    <div class="col-sm-7">
        <div class="form-group">
            <?php

            $options = [
                'placeholder' => 'Введите название...',
                'second-trip-transport-id' => $second_trip_transport->id
            ];

            if($second_trip_transport->active == 0) {
                $options['disabled'] = true;
            }

            echo SelectWidget::widget([
                'model' => $second_trip_transport,
                'attribute' => 'transport_id', // нельзя передавать параметр, иначе не будут установлены автоматические уникальные id
                'name' => 'transport_id',
                'initValueText' => ($second_trip_transport->transport_id > 0 && $second_trip_transport->transport != null ? $second_trip_transport->transport->car_reg_places_count : ''),
                'options' => $options,
                'ajax' => [
                    'url' => '/second-trip-transport/ajax-get-transports-names',
                    'data' => new JsExpression('function(params) {
                        var transports_ids = [];
                        transports_ids.push(1);

                        $("#add-cars-form .trip-transport-row").each(function() {
                            var transport_id = $(this).find(\'input[name="SecondTripTransport[transport_id]"]\').val();
                            if(transport_id.length > 0) {
                                transports_ids.push(transport_id);
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

    <div class="col-sm-1">
        <?php
        if($second_trip_transport->active == 1) {
            echo Html::a(
                '<i class="glyphicon glyphicon-trash"></i>',
                Url::to(['/admin/point/ajax-update', 'id' => $second_trip_transport->id]),
                ['data-original-title' => 'Удалить', 'class' => "btn btn-danger delete-trip-transport"]
            );
        }
        ?>
    </div>
    
    <input type="hidden" name="tt_id[]" value="<?= $second_trip_transport->id ?>">
    
    <div class="col-sm-1">
        
    </div>
</div>
