<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\Order;

if($model->isNewRecord) {
    $count_orders = 0;
}else {
    $count_orders = count($model->futureOrders);
}

//$ors = $model->futureOrders;
//echo '<pre>'; print_r($ors); echo '</pre>';

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);

$start_date = (time() > $model->start_date ? time() : $model->start_date);
?>

<?php $form = ActiveForm::begin([
    'id' => 'tariff-form',
    'options' => [
        'tariff-id' => $model->id,
    ]
]); ?>

<div class="box box-default">
    <div class="box-header scroller-header with-border">
        <button id="update-orders-price" type="button" class="btn btn-sm bg-olive" <?= ($count_orders == 0 ? 'disabled="true"' : '') ?>>
            <i class="glyphicon glyphicon-ruble"></i> Пересчитать стоимость <?= $count_orders ?> заказов отправляемых с <?= date('d.m.Y', $start_date) ?> (только после сохранения изменений в тарифе!)
        </button>
    </div>
</div>


<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Основная информация
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">


        <div class="row">
            <div class="col-sm-12 form-group form-group-sm">
                <?= $form->field($model, 'commercial')->checkbox()->label(false) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 form-group form-group-sm">
                <?php
                if($model->start_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->start_date)) {
                    $model->start_date = date("d.m.Y", $model->start_date);
                }
                echo $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname(), [
                    'removeButton' => false,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ]
                ]);
                ?>
            </div>
        </div>


        <h3>Заказ без предоплаты</h3>

        <div class="row">
            <div class="col-sm-10 form-group form-group-sm">&nbsp;</div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_reservation_cost')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Стоимость бронирования'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_common_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Общий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_student_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Студенческий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_baby_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Детский'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_aero_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('В/из аэропорта'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_parcel_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Посылка'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'unprepayment_loyal_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Призовая поездка'); ?>
            </div>
        </div>





        <h3>Заказ с предоплатой</h3>

        <div class="row">
            <div class="col-sm-10 form-group form-group-sm">&nbsp;</div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_reservation_cost')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Стоимость бронирования'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_common_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Общий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_student_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Студенческий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_baby_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Детский'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_aero_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('В/из аэропорта'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_parcel_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Посылка'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'prepayment_loyal_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Призовая поездка'); ?>
            </div>
        </div>





        <h3>Заказ с предоплатой, супер-цена</h3>

        <div class="row">
            <div class="col-sm-10 form-group form-group-sm">&nbsp;</div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_reservation_cost')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Стоимость бронирования'); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_common_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Общий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_student_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Студенческий'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_baby_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Детский'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_aero_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('В/из аэропорта'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_parcel_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Посылка'); ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'superprepayment_loyal_price')
                    ->textInput()
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'groupSeparator' => ' ',
                            'alias' => 'numeric',
                            'placeholder' => '0',
                            'autoGroup' => true,
                            'digits' => 2,
                            'digitsOptional' => false,
                            'clearMaskOnLostFocus' => true,
                        ],
                        'options' => array_merge(
                            ['class' => 'form-control']
                        )
                    ])->label('Призовая поездка'); ?>
            </div>
        </div>










        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>