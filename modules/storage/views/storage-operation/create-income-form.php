<?php

use app\models\TransportDetailOrigin;
use app\models\TransportDetailState;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\Storage;
use app\models\StorageOperation;
use app\models\StorageOperationType;
use app\models\NomenclatureDetail;
use app\models\StorageDetail;
use app\models\Transport;
use app\models\TransportModel;
use app\models\Driver;
use yii\web\JsExpression;
use app\widgets\SelectWidget;
?>

<div>

    <?php $form = ActiveForm::begin([
        'id' => 'create-income-form',
        'action' => '#',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'options' => [
            'operation-id' => $storage_operation->id
        ],
    ]); ?>


    <div class="row">
        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Дата операции:</label>
        </div>
        <div class="col-sm-2 mini-side-padding nowrap" style="padding-left: 0;">
            <?php
            if($storage_operation->date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $storage_operation->date)) {
                $storage_operation->date = date("d.m.Y", $storage_operation->date);
            }
            echo $form->field($storage_operation, 'date', ['errorOptions' => ['style' => 'display:none;']])
                ->widget(kartik\date\DatePicker::classname(), [
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'class' => ''
                    ],
                    'options' => [
                        'id' => 'date',
                    ],
                    'pluginEvents' => [
                        "changeDate" => "function(e) {
                            //console.log(e);
                            var today = new Date();
                            today.setHours(23, 59, 59, 0); // сегодня, ровно 23:59:59.

                            week_ago = new Date();
                            week_ago.setTime(today.getTime() - 691200000);

                            var date = e.date;
                            //alert('time='+date.getTime());
                            if(date.getTime() > today.getTime()) {
                                alert('нельзя выбрать будущее');
                                $('#create-income-form #date').val('');
                                return false;
                            }

                            if(date.getTime() < week_ago.getTime()) {
                                alert('можно выбирать только даты последних 7 дней');
                                $('#create-income-form #date').val('');
                                return false;
                            }
                        }",
                    ]
                ])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'clientOptions' => [
                        'alias' =>  'dd.mm.yyyy',
                    ],
                    'options' => [
                        'id' => 'date',
                        'start-date' => $storage_operation->date,
                        //'style' => 'width: 80px;',
                        'aria-required' => 'true',
                        'placeholder' => '10.05.2017',
                        'class' => 'form-control'
                    ]
                ])
                ->label(false);
            ?>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">Склад:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($storage_detail, 'storage_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList([0 => '---'] + ArrayHelper::map(Storage::find()->all(), 'id', 'name'), [
                        'id' => 'storage',
                        'class' => 'checkbox form-control',
                        'style' => 'width: 110px;',
                    ])
                    ->label(false); ?>
            </div>
        </div>

        <div class="col-sm-5 mini-side-padding nowrap" style="margin-left: 10px;">
            <label class="label-horizontal">Тип операции</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form
                        ->field($storage_operation, 'operation_type_id', ['errorOptions' => ['style' => 'display:none;']])
                        ->dropDownList(
                            [0 => '---'] + ArrayHelper::map(StorageOperationType::find()->where(['operation_type' => 1])->all(), 'id', 'name'),
                            [
                                'id' => 'operation_type_id',
                                'class' => 'checkbox form-control',
                                'style' => 'width: 326px;',
                            ])
                        ->label(false);
                ?>
            </div>
        </div>
    </div>


    <div class="row">

        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Без участия т/с:</label>
        </div>
        <div class="col-sm-2 mini-side-padding nowrap" style="padding-left: 0;">
            <?= $form
                ->field($storage_operation, 'without_transport')
                ->checkbox(['label' => null, 'style' => 'margin-top: 0;'])
                ->label(false); ?>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">Т/с:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php

                $options = [
                    'name' => 'StorageOperation[transport_id]',
                    'placeholder' => 'Выберите название...',
                    'id' => 'transport_id',
                ];
                if($storage_operation->without_transport == 1) {
                    $options['disabled'] = 'disabled';
                    $options['placeholder'] = '';
                }

                echo $form->field($storage_operation, 'transport_id')->widget(SelectWidget::className(), [
                    'initValueText' => $storage_operation->transport != null ? $storage_operation->transport->name2 : '',
                    'options' => $options,
                    'ajax' => [
                        'url' => '/transport/ajax-get-active-transports',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search
                            };
                        }'),
                    ],
                ])->label(false);
                ?>
            </div>
        </div>


        <div class="col-sm-5 mini-side-padding nowrap" style="margin-left: 10px;">
            <label class="label-horizontal">Водитель:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php
                $options = [
                    'name' => 'StorageOperation[driver_id]',
                    'placeholder' => 'Выберите название...',
                    'id' => 'driver_id',
                ];
                if($storage_operation->without_transport == 1) {
                    $options['disabled'] = 'disabled';
                    $options['placeholder'] = '';
                }

                echo $form->field($storage_operation, 'driver_id')->widget(SelectWidget::className(), [
                    'initValueText' => $storage_operation->driver != null ? $storage_operation->driver->fio : '',
                    'options' => $options,
                    'ajax' => [
                        'url' => '/driver/ajax-get-active-drivers',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search,
                                transport_id: $("input[name=\"StorageOperation[transport_id]\"]").val()
                            };
                        }'),
                    ],
                ])->label(false);
                ?>
            </div>
        </div>
    </div>



    <div class="row" style="margin-top: 12px;">
        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Принадлежность:</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="padding-left: 0; width: 11.5%;">
            <?php
            echo $form->field($nomenclature_detail, 'model_id', ['errorOptions' => ['style' => 'display:none;']])
                ->dropDownList(
                    [0 => '---'] + ArrayHelper::map(TransportModel::find()->all(), 'id', 'name'),
                    [
                        'id' => 'model_id',
                        'class' => 'checkbox form-control',
                        //'style' => 'width: 180px;',
                    ])->label(false);
            ?>
        </div>

        <div class="col-sm-7 mini-side-padding nowrap">
            <label class="label-horizontal">Наименование з/ч:</label>
            <div class="elem-horizontal">
                <?php
                echo $form->field($nomenclature_detail, 'temp_name')->widget(SelectWidget::className(), [
                    //'initValueText' => $initValueText,
                    'value' => $nomenclature_detail->temp_name,
                    'options' => [
                        'name' => 'NomenclatureDetail[temp_name]',
                        'placeholder' => 'Введите название...',
                        'id' => 'nomenclature_detail_temp_name',
                    ],
                    'ajax' => [
                        'url' => '/storage/nomenclature-detail/ajax-get-names',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search
                            };
                        }'),
                    ],
                    'add_new_value_url' => new JsExpression('function(params) {

                        var name = $(".field-nomenclature_detail_temp_name .sw-search").val();
                        var $obj = $(".field-nomenclature_detail_temp_name .sw-element");
                        selectWidgetInsertValue($obj, "0", name);
                    }'),
                ])->label(false);
                ?>
            </div>
        </div>

        <div class="col-sm-1 mini-side-padding nowrap" style="padding-left: 0; padding-right: 0;">
            <label class="label-horizontal">Ед.изм.</label>
            <div class="elem-horizontal">
                <?php
                echo $form->field($detail_measurement_value, 'name')->widget(SelectWidget::className(), [
                    //'initValueText' => $initValueText,
                    'value' => $detail_measurement_value->name,
                    'options' => [
                        'name' => 'DetailMeasurementValue[name]',
                        'placeholder' => 'Выберите',
                        //'id' => 'nomenclature_detail_measurement_value_id',
                        'id' => 'detail_measurement_value_name',
                        //'disabled' => true
                    ],
                    'ajax' => [
                        'url' => '/storage/detail-measurement-value/ajax-get-names',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search
                            };
                        }'),
                    ],
                    'afterRequest' => new JsExpression('function(response) {

                        detail_measurement_value_list = {};
                        for(var i = 0; i < response.results.length; i++) {
                            var detail_measurement = response.results[i];
                            detail_measurement_value_list[detail_measurement.text] = detail_measurement.count_is_double;
                        }

                        // console.log("detail_measurement_value_list:"); console.log(detail_measurement_value_list);
                    }'),
                    'afterChange' => "function(obj, value, text) {
                        updateCountField();
                    }",
                    'add_new_value_url' => new JsExpression('function(params) {
                        var name = $(".field-detail_measurement_value_name .sw-search").val();
                        var $obj = $(".field-detail_measurement_value_name .sw-element");
                        selectWidgetInsertValue($obj, name, name);
                    }'),
                ])->label(false);
                ?>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Место установки:</label>
        </div>
        <div class="col-sm-2 mini-side-padding nowrap" style="padding-left: 0;">
            <?php
            echo $form->field($nomenclature_detail, 'installation_place', ['errorOptions' => ['style' => 'display:none;']])
                ->dropDownList([0 => 'без признака'] + NomenclatureDetail::getInstallationPlaces(), [
                    'id' => 'installation_place',
                    'class' => 'checkbox form-control',
                    //'style' => 'width: 154px;',
                ])
                ->label(false);
            ?>
        </div>


        <div class="col-sm-4 mini-side-padding nowrap">
            <label class="label-horizontal">Сторона установки:</label>
            <div class="elem-horizontal">
                <?php
                echo $form->field($nomenclature_detail, 'installation_side', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList([0 => 'без признака'] + NomenclatureDetail::getInstallationSides(), [
                        'id' => 'installation_side',
                        'class' => 'checkbox form-control',
                        'style' => 'width: 137px;',
                    ])
                    ->label(false);
                ?>
            </div>
        </div>

    </div>


    <div class="row">

        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Состояние з/ч:</label>
        </div>
        <div class="col-sm-2 mini-side-padding nowrap" style="padding-left: 0;">
            <?= $form->field($storage_detail, 'detail_state_id', ['errorOptions' => ['style' => 'display:none;']])
                ->dropDownList(
                    [0 => '---'] + ArrayHelper::map(TransportDetailState::find()->all(), 'id', 'name'),
                    [
                        'id' => 'detail_state_id',
                        'class' => 'checkbox form-control',
                        //'style' => 'width: 200px;',
                    ])->label(false);
            ?>
        </div>

        <div class="col-sm-4 mini-side-padding nowrap">
            <label class="label-horizontal">Происхождение з/ч:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($storage_detail, 'detail_origin_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList(
                        [0 => '---'] + ArrayHelper::map(TransportDetailOrigin::find()->all(), 'id', 'name'),
                        [
                            'id' => 'detail_origin_id',
                            'class' => 'checkbox form-control',
                            'style' => 'width: 138px;',
                        ])->label(false);
                ?>
            </div>
        </div>

    </div>


    <div class="row">

        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Количество:</label>
        </div>
        <div class="col-sm-2 mini-side-padding nowrap" style="padding-left: 0;">
            <?php
            $storage_operation->count = str_replace('.', ',', $storage_operation->count);
            echo $form->field($storage_operation, 'count')
                ->textInput([
                    'class' => 'form-control',
                ])
                ->label(false);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-12 form-group form-group-sm">
            <?= $form->field($storage_operation, 'comment')->textarea(['rows' => 3])->label('Пояснительная записка:') ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-2" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Записать', ['id' => 'create-income-button', 'class' => 'btn btn-success', 'style' => 'padding: 3px 2px;  width: 100%;']) ?>
            </div>
        </div>
        <div class="col-sm-1" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Отменить', ['id' => 'cancel-button', 'class' => 'btn btn-default', 'style' => 'padding: 3px 4px;', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
