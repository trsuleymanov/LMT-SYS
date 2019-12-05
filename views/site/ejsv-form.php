<?php

use app\models\Driver;
use app\widgets\SelectWidget;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$this->registerJsFile('js/site/ejsv-form.js', ['depends'=>'app\assets\AppAsset']);
?>

<div class="ejsv-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'ejsv-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]); ?>

    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Отчитывается водитель:</label>
            <?php
            // echo Html::dropDownList('formula', 0, ArrayHelper::map(Driver::find()->all(), 'id', 'fio'), ['class' => 'form-control', 'id' => 'driver_id']);
            echo SelectWidget::widget([
                //'model' => $searchModel,
                //'attribute' => 'driver_id',
                'name' => 'driver_id',
                //'initValueText' => ($searchModel->driver_id > 0 ? $searchModel->driver->fio : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                ],
                'ajax' => [
                    'url' => '/trip-transport/ajax-get-drivers-names',
                    'data' => new JsExpression('function(params, $obj) {
                        return {
                            search: params.search,
                            // accountability: true
                        };
                    }'),
                ],
                'using_delete_button' => true
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Дата выдачи путевого листа:</label>
            <?php
//            echo DatePicker::widget([
//                //'model' => $pointSearchModel,
//                //'attribute' => 'created_at',
//                'name' => 'date',
//                'value' => date("d.m.Y"),
//                //'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                'type' => DatePicker::TYPE_COMPONENT_APPEND,
//                'pluginOptions' => [
//                    'autoclose' => true,
//                    'format' => 'dd.mm.yyyy',
//                ],
//                'removeButton' => false,
//                'options' => [
//                    'today-date' => date("d.m.Y"),
//                    'datepicker-today-date' => strtotime(date("d.m.Y 03:00:00")).'000'
//                ]
//            ]);
            ?>
            <input type="text" class="form-control" name="date" value="<?= date("d.m.Y") ?>" today-date="<?= date("d.m.Y") ?>" datepicker-today-date="<?= strtotime(date("d.m.Y 03:00:00")).'000' ?>" />
            <?php /*<span class="input-group-addon kv-date-picker" title="Выбрать дату"><i class="glyphicon glyphicon-calendar kv-dp-icon"></i></span> */ ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Т/с, указанное в ПЛ:</label>
            <?php
            echo SelectWidget::widget([
                //'model' => $searchModel,
                //'attribute' => 'transport_id',
                //'initValueText' => ($searchModel->transport_id > 0 ? $searchModel->transport->name3 : ''),
                'name' => 'transport_id',
                'options' => [
                    'placeholder' => 'Введите название...',
                    'disabled' => true,
                ],
                'ajax' => [ // accountability
                    'url' => '/trip-transport/ajax-get-transports-names?trip_id=0&accountability=1',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            format: "name3"
                        };
                    }'),
                ],
                'afterRequest' => "function(response) {
//                    console.log('response:'); console.log(response.results);

//                    storage_detail_hint_list = {};
//                    for(var i in response.results) {
//                        var result = response.results[i];
//
//                        storage_detail_hint_list[result.id] = 'Остаток выбранной запчасти - ' + result.remainder + ' ' + result.measurement_value;
//                    }
                    //$('#storage_detail_id_hint').text('Остаток выбранной запчасти - ' + result.remainder + ' ' + result.measurement_value);

                }",
                'afterChange' => "function(obj, value, text, response_item_data) {
                    // console.log(response_item_data);
                    // alert('accountability=' + response_item_data.accountability);
                    
                    $('#date-end-circle-block').hide();
                    $('#select-notaccountability-transport-circle-block').html('').hide();
                    $('input[name=\'date_end_circle\']').val('');
                    
                    if(value != '') {
                    
                        $('#pl-list-block-1').hide();
                        $('#pl-list-block-2').hide();
                        $('#day_report_transport_circle-block').hide();
                        $('#check-data').html('').hide();
                    
                        if(response_item_data.accountability == 0) {
                            // loadDayReportTransportCircles(); // загружаем список кругов рейсов машины
                            $('#date-end-circle-block').show();
                        }else {
                            loadPls(); // загружаем список путевых листов
                        }
                    }
                }",
                'using_delete_button' => true,
            ])
            ?>
        </div>
    </div>

    <div id="date-end-circle-block" class="row" style="display: none;">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Выберите дату окончания круга</label>
            <input type="text" class="form-control" name="date_end_circle" value="" today-date="<?= date("d.m.Y") ?>" datepicker-today-date="<?= strtotime(date("d.m.Y 03:00:00")).'000' ?>" />
        </div>
    </div>

    <div id="select-notaccountability-transport-circle-block" style="display: none;"></div>

    <div id="pl-list-block-1" class="row" style="display: none;">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Выберите путевой лист</label>
            <div id="pl-list"></div>
        </div>
    </div>

    <div id="pl-list-block-2" class="row" style="display: none;">
        <div class="col-sm-12 form-group form-group-sm">
            <label class="control-label">Введите номер путевого листа, указанный в верхней части ПЛ</label>
            <input type="text" name="pl-number" value="" class="form-control" style="display: inline-block; width: 200px; margin-right: 5px;"><?= Html::button('Ок', ['class' => 'btn btn-info', 'id' => 'set-pl-number', 'style' => 'display: inline-block;']) ?>
        </div>
    </div>

    <div id="day_report_transport_circle-block" style="display: none;"></div>

    <div id="check-data" style="margin-left: 35px; display: none;"></div>

    <div id="check-data-notaccountability-transport" style="margin-left: 35px; display: none;"></div>


    <br />

    <?php ActiveForm::end(); ?>

</div>