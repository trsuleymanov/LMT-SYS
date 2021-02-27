<?php

use app\components\Helper;
use app\models\Access;
use app\models\DayReportTransportCircle;
use app\models\DayReportTripTransport;
use app\models\Direction;
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
use app\models\Trip;
use app\models\TripTransport;
use app\widgets\SelectWidget;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$this->title = 'Путевой лист';
if(!empty($model->number)) {
    $this->title .= ' №'.$model->number;
}
if($model->date_of_issue > 0) {
    $this->title .= ' от '.date("d.m.Y", $model->date_of_issue).', '.Helper::getWeekDay($model->date_of_issue);
}
if($model->transport_id > 0 && $model->transport != null) {
    $this->title .= ' / '.$model->transport->sh_model.' '.$model->transport->car_reg;
}
if($model->driver_id > 0 && $model->driver != null) {
    $this->title .= ' / '.$model->driver->fio;
}




$this->registerJsFile('js/site/waybill.js', ['depends'=>'app\assets\AppAsset']);
$this->registerJsFile('js/site/waybill-update.js', ['depends'=>'app\assets\AppAsset']);
$this->registerCssFile('css/waybill.css', ['depends'=>'app\assets\AppAsset']);


$transport_expenses_seller_types = TransportExpensesSellerType::find()->all();
$aTransportExpensesSellerTypes = ArrayHelper::map($transport_expenses_seller_types, 'name', 'id');

$aTrExpensesTypical = [];
$aTrExpensesOther = [];
$aTrExpensesIncomingPaymentRequests = [];
foreach($transport_expenses as $tr_expenses) {
    if($tr_expenses->view_group == 'typical_expenses') {
        $aTrExpensesTypical[] = $tr_expenses;
    }elseif($tr_expenses->view_group == 'other_expenses') {
        $aTrExpensesOther[] = $tr_expenses;
    }elseif($tr_expenses->view_group == 'incoming_payment_requests') {
        $aTrExpensesIncomingPaymentRequests[] = $tr_expenses;
    }
}



$aStartTripsNames = []; // рейсы для текущего дня и текущей машины отправляющихся из города базирования
$aEndTripsNames = []; // рейсы для текущей машины и дней(текущий и следующий) отправляющихся из города небазирования


if($model->transport_id > 0 && $model->date_of_issue > 0) {

    // определим направление "базирование" - это направление в котогом город отправления равен городу базирования транспорта
    if(empty($model->transport->base_city_id)) {
        throw new ForbiddenHttpException('Город базирования у машины не найден');
    }
    $base_direction = Direction::find()->where(['city_from' => $model->transport->base_city_id])->one();
    if($base_direction == null) {
        throw new ForbiddenHttpException('Город базирования не найден в направлениях');
    }


    $base_trips = Trip::find()
        ->where(['date' => $model->date_of_issue])
        ->andWhere(['direction_id' => $base_direction->id])
        ->all();
    $aBaseTrips = ArrayHelper::index($base_trips, 'id');
    $aBaseTripsIds = ArrayHelper::map($base_trips, 'id', 'id');

    $base_trip_transports = TripTransport::find()
        ->where(['transport_id' => $model->transport_id])
        ->andWhere(['trip_id' => $aBaseTripsIds])
        ->andWhere(['status_id' => 1])
        ->all();
    foreach($base_trip_transports as $trip_transport) {
        $trip =  $aBaseTrips[$trip_transport->trip_id];
        $aStartTripsNames[$trip_transport->id] = ($trip->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip->name;
        //$aEndTripsNames[$trip_transport->id] = ($trip->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip->name;
    }

    //$tomorrow_unixtime = strtotime(date("d.m.Y", $model->date_of_issue)) + 86400;

    $notbase_direction = Direction::find()->where(['!=', 'city_from', $model->transport->base_city_id])->one();
    if($notbase_direction == null) {
        throw new ForbiddenHttpException('Город небазирования не найден в направлениях');
    }

    $notbase_trips = Trip::find()
        ->where(['date' => [$model->date_of_issue, $model->date_of_issue + 86400]])
        ->andWhere(['direction_id' => $notbase_direction->id])
        ->all();
    $aNotBaseTrips = ArrayHelper::index($notbase_trips, 'id');
    $aNotBaseTripsIds = ArrayHelper::map($notbase_trips, 'id', 'id');

    $notbase_trip_transports = TripTransport::find()
        ->where(['transport_id' => $model->transport_id])
        ->andWhere(['trip_id' => $aNotBaseTripsIds])
        ->andWhere(['status_id' => 1])
        ->all();
    foreach($notbase_trip_transports as $trip_transport) {
        $trip =  $aNotBaseTrips[$trip_transport->trip_id];
        $aEndTripsNames[$trip_transport->id] = ($trip->direction_id == 1 ? 'АК ' : 'КА ').' '.$trip->name . ' ('.date('d.m.Y', $trip->date).')';
    }
}


?>

<div class="transport-waybill-form">

    <?php $form = ActiveForm::begin([
        'id' => 'waybill-form',
        'options' => [
            'transport-waybill-id' => $model->id,
        ],
    ]); ?>

    <div class="box box-solid">

        <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-9 form-group-sm">
                    <h3 class="box-title">
                        <?= $this->title ?>
                    </h3>
                </div>
                <div class="col-sm-3 form-group-sm">
                    <a href="/waybill/transport-waybill/list" style="display: inline-block; margin-top: 20px;">Список путевых листов</a>
                </div>

            </div>
            <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
                <br />
                <div class="row">
                    <div class="col-sm-9 form-group-sm">
                        <?= $form->field($model, 'is_visible')->checkbox(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="box-body">

            <div class="waybill-block">
                <div class="waybill-title waybill-block-toogle">Эксплуатация <span class="waybill-arrow"><i class="glyphicon glyphicon-menu-down"></i></span></div>
                <div class="waybill-body">

                    <div class="row">
                        <div class="col-v-11 form-group-sm">
                            <?php
                            echo $form->field($model, 'number', [
                                //'template' => '{label}<br />{input}'
                                'errorOptions' => ['style' => 'display:none;'],
                                'inputOptions' => [
                                    'class' => 'form-control',
                                    'disabled' => true,
                                ]
                            ])->textInput(['maxlength' => true,]);
                            ?>
                        </div>

                        <div class="col-v-11 form-group-sm">
                            <?php
                            if($model->date_of_issue > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date_of_issue)) {
                                $model->date_of_issue = date("d.m.Y", $model->date_of_issue);
                            }
                            echo $form->field($model, 'date_of_issue', ['errorOptions' => ['style' => 'display:none;']])
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

                        <div class="col-v-27 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_comment')->textarea(['rows' => 2]);
                            ?>
                        </div>
                    </div>


                    <hr />
                    <div class="row">
                        <div class="col-v-100">Перед рейсом:</div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-v-11 form-group-sm">
                            <label>Мед.осмотр</label>
                            <?= $form->field($model, 'pre_trip_med_check')->checkbox(['label' => false])->label(false); ?>
                        </div>

                        <div class="col-v-11 form-group-sm">
                            <?php
                            $options = [
                                'class' => 'form-control'
                            ];
                            if($model->pre_trip_med_check != 1) {
                                $options['disabled'] = true;
                            }
                            echo $form->field($model, 'pre_trip_med_check_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_',
                                        //'disabled' => true,
                                    ],
                                    'options' => $options
                                ])->label('Время');
                            ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-14 form-group-sm">
                            <label>Тех.осмотр</label>
                            <?= $form->field($model, 'pre_trip_tech_check')->checkbox(['label' => false])->label(false); ?>
                        </div>

                        <div class="col-v-11 form-group-sm">
                            <?php

                            $options = [
                                'class' => 'form-control'
                            ];
                            if($model->pre_trip_tech_check != 1) {
                                $options['disabled'] = true;
                            }

                            echo $form->field($model, 'pre_trip_tech_check_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_'
                                    ],
                                    'options' => $options

                                ])->label('Время');
                            ?>
                        </div>

                        <div class="col-v-6">&nbsp;</div>

                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'departure_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_'
                                    ]
                                ])->label('Время выезда');
                            ?>
                        </div>

                        <div class="col-v-17 form-group-sm">
                            <?= $form->field($model, 'mileage_before_departure', [
                                'errorOptions' => ['style' => 'display:none;']
                            ])
                                ->textInput()
//                                ->widget(\yii\widgets\MaskedInput::class, [
//                                    'mask' => '9999',
//                                    'clientOptions' => [
//                                        'placeholder' => '_'
//                                    ]
//                                ])
                                ->label('Показания пробега') ?>
                        </div>
                    </div>

                    <br />
                    <div class="row">

                        <div class="col-v-17 form-group-sm">
                            <?php
                            // echo $form->field($model, 'trip_transport_start')->textInput();
                            if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date_of_issue)) {
                                $model->date_of_issue = strtotime($model->date_of_issue);
                            }


                            echo $form
                                ->field($model, 'trip_transport_start', ['errorOptions' => ['style' => 'display:none;']])
                                ->dropDownList([0 => 'Нет'] + $aStartTripsNames, [])
                                ->label('Выбрать начало');
                            ?>
                        </div>

                        <div class="col-v-8">&nbsp;</div>

                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form
                                ->field($model, 'trip_transport_end', ['errorOptions' => ['style' => 'display:none;']])
                                ->dropDownList([0 => 'Нет'] + $aEndTripsNames, [])
                                ->label('Выбрать окончание');
                            ?>
                        </div>
                    </div>


                    <br />
                    <div class="row">
                        <div class="col-v-100">После рейса:</div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-v-11 form-group-sm">
                            <label>Мед.осмотр</label>
                            <?= $form->field($model, 'after_trip_med_check')->checkbox(['label' => false])->label(false); ?>
                        </div>

                        <div class="col-v-11 form-group-sm">
                            <?php
                            $options = [
                                'class' => 'form-control'
                            ];
                            if($model->after_trip_med_check != 1) {
                                $options['disabled'] = true;
                            }
                            echo $form->field($model, 'after_trip_med_check_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_'
                                    ],
                                    'options' => $options
                                ])->label('Время');
                            ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-14 form-group-sm">
                            <label>Тех.осмотр</label>
                            <?= $form->field($model, 'after_trip_tech_check')->checkbox(['label' => false])->label(false); ?>
                        </div>

                        <div class="col-v-11 form-group-sm">
                            <?php
                            $options = [
                                'class' => 'form-control'
                            ];
                            if($model->after_trip_tech_check != 1) {
                                $options['disabled'] = true;
                            }
                            echo $form->field($model, 'after_trip_tech_check_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_'
                                    ],
                                    'options' => $options
                                ])->label('Время');
                            ?>
                        </div>

                        <div class="col-v-6">&nbsp;</div>

                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'return_time', ['errorOptions' => []])
                                ->widget(\yii\widgets\MaskedInput::class, [
                                    'mask' => '99 : 99',
                                    'clientOptions' => [
                                        'placeholder' => '_'
                                    ]
                                ])->label('Время возврата');
                            ?>
                        </div>

                        <div class="col-v-17 form-group-sm">
                            <?= $form->field($model, 'mileage_after_departure', [
                                'errorOptions' => ['style' => 'display:none;']
                            ])->textInput()->label('Показания пробега') ?>
                        </div>
                    </div>


                    <br />
                    <div class="row">
                        <div class="col-v-100"><b>Отметки об исполнении нормативных требований к документации:</b></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-v-10 form-group-sm">
                            <?= $form
                                ->field($model, 'waybill_state', [
                                    'errorOptions' => ['style' => 'display:none;'],
                                ])
                                ->dropDownList(
                                    [0 => ''] + TransportWaybill::getWaybillStates(),
                                    []);
                            ?>
                        </div>
                        <div class="col-v-1">&nbsp;</div>
                        <div class="col-v-10 form-group-sm">
                            <?= $form
                                ->field($model, 'values_fixed_state', [
                                    'errorOptions' => ['style' => 'display:none;'],
                                ])
                                ->dropDownList(
                                    [0 => ''] + TransportWaybill::getValuesFixedStates(),
                                    []);
                            ?>
                        </div>
                        <div class="col-v-1">&nbsp;</div>

                        <div class="col-v-10 form-group-sm">
                            <?= $form
                                ->field($model, 'gsm', [
                                    'errorOptions' => ['style' => 'display:none;'],
                                ])
                                ->dropDownList(
                                    [0 => ''] + TransportWaybill::getGsms(),
                                    []);
                            ?>
                        </div>
                        <div class="col-v-4">&nbsp;</div>

                        <div class="col-v-17 form-group-sm">
                            <?= $form
                                ->field($model, 'klpto', [
                                    'errorOptions' => ['style' => 'display:none;'],
                                ])
                                ->dropDownList(
                                    [0 => ''] + TransportWaybill::getKlpto(),
                                    []);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?php
                            echo $form->field($model, 'klpto_comment')->textarea(['rows' => 2]);
                            ?>
                        </div>
                    </div>



                    <br />
                    <div class="row">
                        <div class="col-v-100"><b>События на рейсе:</b></div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-v-2 waybill-vertical-title">С1</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event1_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event1_id > 0 ? $model->tripEvent1->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event1_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-2 waybill-vertical-title">С5</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event5_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event5_id > 0 ? $model->tripEvent5->name.'sdf' : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event5_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-v-2 waybill-vertical-title">С2</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event2_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event2_id > 0 ? $model->tripEvent2->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event2_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-2 waybill-vertical-title">С6</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event6_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event6_id > 0 ? $model->tripEvent6->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event6_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-v-2 waybill-vertical-title">С3</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event3_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event3_id > 0 ? $model->tripEvent3->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event3_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-2 waybill-vertical-title">С7</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event7_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event7_id > 0 ? $model->tripEvent7->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event7_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-v-2 waybill-vertical-title">С4</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event4_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event4_id > 0 ? $model->tripEvent4->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event4_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                        <div class="col-v-3">&nbsp;</div>

                        <div class="col-v-2 waybill-vertical-title">С8</div>
                        <div class="col-v-17 form-group-sm">
                            <?php
                            echo $form->field($model, 'trip_event8_id')->widget(SelectWidget::className(), [
                                'initValueText' => ($model->trip_event8_id > 0 ? $model->tripEvent8->name : ''),
                                'options' => [
                                    'placeholder' => 'Введите название...',
                                ],
                                'data' => [0 => 'Нет'] + ArrayHelper::map(TransportWaybillTripEvents::find()->all(), 'id', 'name'),
                            ])->label(false);
                            ?>
                        </div>

                        <div class="col-v-27 form-group-sm">
                            <?= $form->field($model, 'trip_event8_comment')->textarea(['rows' => 2])->label(false); ?>
                        </div>

                    </div>

                </div>
            </div>


            <br />
            <div class="waybill-block">

                <div class="waybill-title waybill-block-toogle">Учет расходов <span class="waybill-arrow"><i class="glyphicon glyphicon-menu-down"></i></span></div>
                <div class="waybill-body">

                <?php if(Access::hasUserAccess('access_to_delivery_of_proceeds', 'page_part'))
                { ?>
                    <p><b>Типовые расходы из выручки:</b></p>
                    <div class="expenses-table-over">
                        <div class="expenses-table-inner">
                            <table class="transport-expenses-table">
                                <tr style="background-color: #E6FFE6;">
                                    <td class="expenses_seller_type_id_col allotted" style="background-color: #E6FFE6;">
                                        За что
                                    </td>
                                    <td class="expenses_seller_id_col">Продавец</td>
                                    <td class="count_col">К-во</td>
                                    <td class="price_col">Сумма</td>
                                    <td class="points_col">Баллы</td>
                                    <td class="expenses_doc_type_id_col allotted">Вид ДО</td>
                                    <td class="expenses_type_id_col allotted">ДО</td>
                                    <td class="doc_number_col allotted">№ ДО</td>
                                    <td class="need_pay_date_col allotted">Дата ДО</td>
                                    <td class="check_attached_col">Чек</td>
                                    <td class="expenses_is_taken_col">?РП</td>
                                    <td class="expenses_is_taken_comment_col">КРП</td>
                                    <td class="payment_date_col green-allotted">Дата оплаты</td>
                                    <td class="payment_method_id_col green-allotted">Способ</td>
                                    <td class="transport_expenses_paymenter_id_col green-allotted">Кто оплатил</td>
                                    <td class="payment_comment_col green-allotted">КО</td>
                                    <td></td>
                                </tr>

                                <?php
                                $num = 1;
                                foreach ($aTrExpensesTypical as $tr_expenses) {
                                    echo $this->render('transport_expenses_row', [
                                        'tr_expenses' => $tr_expenses,
                                        'form' => $form,
                                        'delete_row' => true,
                                        'num' => $num,
                                    ]);
                                    $num++;
                                }
                                ?>
                            </table>
                        </div>
                    </div>


                    <br/><br/>
                    <p><b>Прочие расходы из выручки:</b></p>
                    <div class="expenses-table-over">
                        <div class="expenses-table-inner">
                            <table class="transport-expenses-table">
                                <tr style="background-color: #FFF9B4;">
                                    <td class="expenses_seller_type_id_col allotted" style="background-color: #FFF9B4;">
                                        За что
                                    </td>
                                    <td class="expenses_seller_id_col">Продавец</td>
                                    <td class="count_col">К-во</td>
                                    <td class="price_col">Сумма</td>
                                    <td class="points_col">Баллы</td>
                                    <td class="expenses_doc_type_id_col allotted">Вид ДО</td>
                                    <td class="expenses_type_id_col allotted">ДО</td>
                                    <td class="doc_number_col allotted">№ ДО</td>
                                    <td class="need_pay_date_col allotted">Дата ДО</td>
                                    <td class="check_attached_col">Чек</td>
                                    <td class="expenses_is_taken_col">?РП</td>
                                    <td class="expenses_is_taken_comment_col">КРП</td>
                                    <td class="payment_date_col green-allotted">Дата оплаты</td>
                                    <td class="payment_method_id_col green-allotted">Способ</td>
                                    <td class="transport_expenses_paymenter_id_col green-allotted">Кто оплатил</td>
                                    <td class="payment_comment_col green-allotted">КО</td>
                                    <td></td>
                                </tr>

                                <?php
                                $num = 1;
                                foreach ($aTrExpensesOther as $tr_expenses) {
                                    echo $this->render('transport_expenses_row', [
                                        'tr_expenses' => $tr_expenses,
                                        'form' => $form,
                                        'delete_row' => true,
                                        'num' => $num,
                                    ]);
                                    $num++;
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', [''], ['class' => 'btn btn-success add-transport-expenses', 'table-type' => 'other']) ?>


                    <br/><br/>
                    <p><b>Входящие требования оплаты:</b></p>
                    <div class="expenses-table-over">
                        <div class="expenses-table-inner">
                            <table class="transport-expenses-table">
                                <tr style="background-color: #F2F2F2;">
                                    <td class="expenses_seller_type_id_col allotted" style="background-color: #F2F2F2;">
                                        За что
                                    </td>
                                    <td class="expenses_seller_id_col">Продавец</td>
                                    <td class="count_col">К-во</td>
                                    <td class="price_col">Сумма</td>
                                    <td class="points_col">Баллы</td>
                                    <td class="expenses_doc_type_id_col allotted">Вид ДО</td>
                                    <td class="expenses_type_id_col allotted">ДО</td>
                                    <td class="doc_number_col allotted">№ ДО</td>
                                    <td class="need_pay_date_col allotted">Дата ДО</td>
                                    <td class="check_attached_col">Чек</td>
                                    <td class="expenses_is_taken_col">?РП</td>
                                    <td class="expenses_is_taken_comment_col">КРП</td>
                                    <td class="payment_date_col green-allotted">Дата оплаты</td>
                                    <td class="payment_method_id_col green-allotted">Способ</td>
                                    <td class="transport_expenses_paymenter_id_col green-allotted">Кто оплатил</td>
                                    <td class="payment_comment_col green-allotted">КО</td>
                                    <td></td>
                                </tr>

                                <?php
                                $num = 1;
                                //echo "aTrExpensesIncomingPaymentRequests:<pre>"; print_r($aTrExpensesIncomingPaymentRequests); echo "</pre>";
                                foreach ($aTrExpensesIncomingPaymentRequests as $tr_expenses) {
                                    echo $this->render('transport_expenses_row', [
                                        'tr_expenses' => $tr_expenses,
                                        'form' => $form,
                                        'delete_row' => true,
                                        'num' => $num,
                                    ]);
                                    $num++;
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                    <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', [''], ['class' => 'btn btn-success add-transport-expenses', 'table-type' => 'incoming-payment-requests']) ?>
                </div>

                <?php
                }else {
                    echo 'Нет доступа <br />';
                }
                ?>
            </div>

            <div id="preliminary-results-block">
                <?= $this->render('_preliminary_results_block', [
                    'form' => $form,
                    'model' => $model
                ]) ?>
            </div>

            <div id="correct-block">
                <?= $this->render('_correct_block', [
                    'form' => $form,
                    'model' => $model
                ]) ?>
            </div>

            <div id="accruals-block">
                <?= $this->render('_accruals_block', [
                    'form' => $form,
                    'model' => $model
                ]) ?>
            </div>

        </div>


    </div>

    <?php ActiveForm::end(); ?>

</div>
