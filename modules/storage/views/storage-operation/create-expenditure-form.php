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
        'id' => 'create-expenditure-form',
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
                                $('#create-expenditure-form #date').val('');
                                return false;
                            }

                            if(date.getTime() < week_ago.getTime()) {
                                alert('можно выбирать только даты последних 7 дней');
                                $('#create-expenditure-form #date').val('');
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
                <?php
                echo Html::dropDownList('storage_id', $selected_storage_id, [0 => '---'] + ArrayHelper::map(Storage::find()->all(), 'id', 'name'), [
                    'id' => 'storage',
                    'class' => 'checkbox form-control',
                    'style' => 'width: 110px;',
                ]);

//                echo $form->field($storage_detail, 'storage_id', ['errorOptions' => ['style' => 'display:none;']])
//                    ->dropDownList([0 => '---'] + ArrayHelper::map(Storage::find()->all(), 'id', 'name'), [
//                        'id' => 'storage',
//                        'class' => 'checkbox form-control',
//                        'style' => 'width: 110px;',
//                    ])
//                    ->label(false);
                ?>
            </div>
        </div>

        <div class="col-sm-5 mini-side-padding nowrap" style="margin-left: 10px;">
            <label class="label-horizontal">Тип операции</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($storage_operation, 'operation_type_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList(
                        ['' => '---'] + ArrayHelper::map(StorageOperationType::find()->where(['operation_type' => 0])->all(), 'id', 'name'),
                        [
                            'id' => 'operation_type_id',
                            'class' => 'checkbox form-control',
                            'style' => 'width: 312px;',
                        ])->label(false);
                ?>
            </div>
        </div>
    </div>


    <div class="row">

        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Запчасть:</label>
        </div>
        <div class="col-sm-7 mini-side-padding nowrap" style="padding-left: 0;">
            <?php

            $aStorageDetails = [];
            /*
            $storage_details = StorageDetail::find()->where(['>', 'storage_place_count', 0])->all();
            $aNomenclatureDetails = [];
            $aTransportModels = [];
            if(count($storage_details) > 0) {
                $aNomenclatureDetails = ArrayHelper::map(NomenclatureDetail::find()->where(['id' => ArrayHelper::map($storage_details, 'nomenclature_detail_id', 'nomenclature_detail_id')])->all(), 'id', 'name');
                $aTransportModels = ArrayHelper::map(TransportModel::find()->where(['id' => ArrayHelper::map($storage_details, 'model_id', 'model_id')])->all(), 'id', 'name');
                $aDetailStates = ArrayHelper::map(TransportDetailState::find()->where(['id' => ArrayHelper::map($storage_details, 'detail_state_id', 'detail_state_id')])->all(), 'id', 'name');
                $aDetailOrigins = ArrayHelper::map(TransportDetailOrigin::find()->where(['id' => ArrayHelper::map($storage_details, 'detail_origin_id', 'detail_origin_id')])->all(), 'id', 'name');
            }
            //echo "aNomenclatureDetails:<pre>"; print_r($aNomenclatureDetails); echo "</pre>";


            foreach($storage_details as $storage_detail) {
                $aStorageDetails[$storage_detail->id] =
                    $aNomenclatureDetails[$storage_detail->nomenclature_detail_id]
                    .' - '.$aTransportModels[$storage_detail->model_id]
                    .' - '.StorageDetail::getInstallationPlaces()[$storage_detail->installation_place]
                    .' - '.StorageDetail::getInstallationSides()[$storage_detail->installation_side]
                    .' - '.$aDetailStates[$storage_detail->detail_state_id]
                    .' - '.$aDetailOrigins[$storage_detail->detail_origin_id]
                    .' - '.$storage_detail->storage_place_count . ' штук';
            }*/

//            echo $form->field($storage_operation, 'storage_detail_id', ['errorOptions' => ['style' => 'display:none;']])
//                ->dropDownList([0 => '---'] + $aStorageDetails, [
//                    'id' => 'storage_detail_id',
//                    'class' => 'checkbox',
//                ])
//                ->label(false);

            $storage_detail = $storage_operation->storageDetail;
            if($storage_detail == null) {
                $initValueText = '';
            }else {
                $nomenclature_detail = $storage_detail->nomenclatureDetail;
                $transport_model = $nomenclature_detail->transportModel;
                $detail_state = $storage_detail->transportDetailState;
                $detail_origin = $storage_detail->transportDetailOrigin;
                $initValueText = $storage_detail->getDetailText($nomenclature_detail, $transport_model, $detail_state->name, $detail_origin->name);
            }

            echo $form->field($storage_operation, 'storage_detail_id')->widget(SelectWidget::className(), [
                'initValueText' => $initValueText,
                //'value' => '',
                'options' => [
                    'name' => 'StorageOperation[storage_detail_id]',
                    'placeholder' => 'Введите название...',
                    'id' => 'storage_detail_id',
                ],
                'ajax' => [
                    'url' => '/storage/storage-operation/ajax-get-storage-detail-list',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            storage_id: $("#create-expenditure-form #storage").val()
                        };
                    }'),
                ],
                'afterChange' => "function(obj, value, text) {
                    if(storage_detail_hint_list[value] != undefined && storage_detail_hint_list[value].length > 0) {
                        $('#storage_detail_id_hint').text(storage_detail_hint_list[value]);
                    }else {
                        $('#storage_detail_id_hint').text('');
                    }
                }",
                'afterRequest' => "function(response) {
                    //console.log('response:'); console.log(response.results);

                    storage_detail_hint_list = {};
                    for(var i in response.results) {
                        var result = response.results[i];

                        storage_detail_hint_list[result.id] = 'Остаток выбранной запчасти - ' + result.remainder + ' ' + result.measurement_value;
                    }
                    //$('#storage_detail_id_hint').text('Остаток выбранной запчасти - ' + result.remainder + ' ' + result.measurement_value);

                }",
            ])->label(false);
            ?>

            <div id="storage_detail_id_hint"></div>
        </div>


        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">Кол-во</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($storage_operation, 'count')
                    ->textInput([
                        'class' => 'form-control'
                    ])
//                    ->widget(\yii\widgets\MaskedInput::class, [
//                        'mask' => '999',
//                        'clientOptions' => [
//                            'showMaskOnFocus' => false,
//                            'showMaskOnHover' => false
//                        ],
//                        'options' => [
//                            'class' => "form-control",
//                            'style' => 'width: 60px;'
//                        ]
//                    ])
                    ->label(false);
                ?>
            </div>
        </div>

    </div>


    <div class="row" style="margin-top: 10px;">

        <div class="col-sm-2 first-col">
            <label class="label-horizontal">Без участия т/с:</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="padding-left: 0;">
            <?= $form
                ->field($storage_operation, 'without_transport')
                ->checkbox(['label' => null, 'style' => 'margin-top: 0;'])
                ->label(false); ?>
        </div>

        <div class="col-sm-3 mini-side-padding nowrap">
            <label class="label-horizontal">Т/с:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php
                $options = [
                    'name' => 'StorageOperation[transport_id]',
                    'placeholder' => 'Введите название...',
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

        <div class="col-sm-4 mini-side-padding nowrap">
            <label class="label-horizontal">Водитель:</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php
                $options = [
                    'name' => 'StorageOperation[driver_id]',
                    'placeholder' => 'Введите название...',
                    'id' => 'driver_id',
                ];
                if($storage_operation->without_transport == 1) {
                    $options['disabled'] = 'disabled';
                    $options['placeholder'] = '';
                }
                echo $form->field($storage_operation, 'driver_id')->widget(SelectWidget::className(), [
                    //'value' => $storage_operation->driver != null ? $storage_operation->driver->fio : '',
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

    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-11 mini-side-padding">
            <label class="label-vertical">Пояснительная записка:</label>
            <?= $form->field($storage_operation, 'comment', ['errorOptions' => ['style' => 'display:none;']])
                ->textarea([
                    'class' => 'input-text',
                    'style' => 'width: 100%;'
                ])
                ->label(false);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-2" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Записать', ['id' => 'create-expenditure-button', 'class' => 'btn btn-success', 'style' => 'padding: 3px 2px;  width: 100%;']) ?>
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