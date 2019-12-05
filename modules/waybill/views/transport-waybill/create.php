<?php

use app\models\Driver;
use app\models\Transport;
use app\models\TransportExpensesDocType;
use app\models\TransportExpensesPaymenter;
use app\models\TransportExpensesSeller;
use app\models\TransportExpensesSellerType;
use app\models\TransportExpensesTypes;
use app\models\TransportPaymentMethods;
use app\models\TransportWaybill;
use app\models\TransportWaybillTripEvents;
use app\widgets\SelectWidget;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$this->title = 'Путевой лист';

$this->registerJsFile('js/site/waybill-create.js', ['depends'=>'app\assets\AppAsset']);


?>

<div class="transport-waybill-form-create">

    <?php $form = ActiveForm::begin([
        'id' => 'waybill-form-create',
        'options' => [
            //'transport-waybill-id' => $model->id,
        ],
    ]); ?>

    <div class="box box-solid">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-7 form-group-sm">
                    <h3 class="box-title" style="width: 50%;">
                        <?= $this->title ?>
                    </h3>
                </div>
                <div class="col-sm-5 form-group-sm">
                    <a href="/waybill/transport-waybill/list" style="display: inline-block; margin-top: 20px;">Список путевых листов</a>
                </div>
            </div>
        </div>

        <div class="box-body">
            <br />
            <div class="row">
                <div class="col-v-11 form-group-sm">
                    <?php
                    echo $form->field($model, 'number', [
                        //'template' => '{label}<br />{input}'
                        //'errorOptions' => ['style' => 'display:none;']
                    ])->textInput(['maxlength' => true]);
                    ?>
                </div>

                <div class="col-v-11 form-group-sm">
                    <?php
                    if($model->date_of_issue > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date_of_issue)) {
                        $model->date_of_issue = date("d.m.Y", $model->date_of_issue);
                    }
                    echo $form->field($model, 'date_of_issue', [
                        //'errorOptions' => ['style' => 'display:none;']
                    ])
                        ->widget(kartik\date\DatePicker::classname(), [
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'format' => 'dd.mm.yyyy',
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'class' => ''
                            ],
                        ])
                        ->widget(\yii\widgets\MaskedInput::class, [
                            'clientOptions' => [
                                'alias' =>  'dd.mm.yyyy',
                            ],
                            'options' => [
                                'aria-required' => 'true',
                                'placeholder' => '10.05.2017',
                                'class' => 'form-control'
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-v-3">&nbsp;</div>

                <div class="col-v-14 form-group-sm">
                    <label>Транспорт</label>
                    <?php

                    // здесь нужно отображать только те машины, которые были отправлены в этот день!
                    echo SelectWidget::widget([
                        'model' => $model,
                        'attribute' => 'transport_id',
                        'name' => 'transport_id',
                        'initValueText' => ($model->transport_id > 0 && $model->transport != null ? $model->transport->car_reg_places_count : ''),
                        'options' => [
                            'placeholder' => 'Введите название...',
                        ],
                        'ajax' => [
                            'url' => '/trip-transport/ajax-get-transports-names',
                            'data' => new JsExpression('function(params) {
                                        return {
                                            search: params.search
                                        };
                                    }'),
                        ],
                        'using_delete_button' => false
                    ]);
                    ?>
                </div>

                <div class="col-v-14 form-group-sm">
                    <label>Водитель</label>
                    <?php
                    echo SelectWidget::widget([
                        'model' => $model,
                        'attribute' => 'driver_id',
                        'name' => 'driver_id',
                        'initValueText' => ($model->driver_id > 0 && $model->driver != null ? $model->driver->fio : ''),
                        'options' => [
                            'placeholder' => 'Введите название...',
                        ],
                        'ajax' => [
                            'url' => '/trip-transport/ajax-get-drivers-names',
                            'data' => new JsExpression('function(params, $obj) {

                                        var selected_driver_id = $obj.find("input[name=\"TransportWaybill[driver_id]\"]").val();
                                        var selected_transport_id = $("input[name=\"TransportWaybill[transport_id]\"]").val();

                                        return {
                                            search: params.search,
                                            selected_driver_id: selected_driver_id,
                                            selected_transport_id: selected_transport_id
                                        };
                                    }'),
                        ],
                        'using_delete_button' => false
                    ]);
                    ?>
                </div>
                <div class="col-v-2">&nbsp;</div>
                <div class="col-v-20 form-group-sm" style="margin-top: 22px;">
                    <?= Html::submitButton('Зафиксировать ПЛ', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
