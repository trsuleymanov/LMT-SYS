<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\models\OrderStatus;
use app\models\OrderCancellationReason;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Point;
use app\models\Trip;
use app\models\Order;
use kartik\select2\Select2;
use yii\web\JsExpression;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-sm-4 form-group form-group-sm">
            <?php
//            if($model->date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date)) {
//                $model->date = date("d.m.Y", $model->date);
//            }
//            echo $form->field($model, 'date')->widget(kartik\date\DatePicker::classname(), [
//                'pluginOptions' => [
//                    'format' => 'dd.mm.yyyy',
//                    'todayHighlight' => true,
//                    'autoclose' => true,
//                ],
//                //'options' => ['disabled' => true]
//            ]);
            ?>

            <div class="form-group field-order-date">
                <label class="control-label" for="order-date">Дата заказа</label>
                <div  class="input-group date"><?= ($model->date > 0 ? date('d.m.Y', $model->date) : '') ?></div>
            </div>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'status_id')->dropDownList([0 => ''] + ArrayHelper::map(OrderStatus::find()->all(), 'id', 'name')); ?>
            <?= $form->field($model, 'cancellation_reason_id')->dropDownList([0 => ''] + ArrayHelper::map(OrderCancellationReason::find()->all(), 'id', 'name')); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">

            <?php
            //echo $form->field($model, 'client_id')->dropDownList(
            //    ArrayHelper::map(Client::find()->all(), 'id', 'name')
            //); ?>

            <!-- Клиент -->
            <?php
            $createClient = ' ' . Html::a('&nbsp;<i class="fa fa-lg fa-plus-circle text-black full-opacity-hover"></i>', '/admin/client/create', [
                    'target' => '_blank',
                    'class' => 'pull-right',
                    'data-toggle' => 'tooltip',
                    'title' => 'Создать клиента'
                ]);

            echo $form->field($model, 'client_id')->widget(Select2::className(), [
                'options' => ['placeholder' => 'Введите имя...', 'id' => 'client_id', 'disabled' => true],
                'initValueText' => ($model->client_id > 0 ? $model->client->name : ''),
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => '/admin/client/ajax-client',
                        'dataType' => 'json',
                        'type' => 'POST',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.term
                            }
                        }'),
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(item) { return item.text; }'),
                    'templateSelection' => new JsExpression('function (item) {
                        if (item.id.length > 0) {
                            $(".update-client").show();
                        }else {
                            $(".update-client").hide();
                        }
                        return item.text;
                    }'),
                ],
            ])->label('Клиент' . $createClient); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'time_air_train_arrival')->textInput() ?>
        </div>
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'time_air_train_departure')->textInput() ?>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-4 form-group form-group-sm">
            <?php //= $form->field($model, 'plan_trip_transport_id')->textInput() ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'fact_trip_transport_id')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'point_id_from')->dropDownList(
                [0 => ''] + $point_list
            ); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'point_id_to')->dropDownList(
                [0 => ''] + $point_list
            ); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'prize_trip_count')->textInput(); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php // а может лучше так: $trips = Trip::getTrips(strtotime($order->date), $order->direction_id); ?>
            <?= $form->field($model, 'trip_id')->dropDownList(
                [0 => ''] + ArrayHelper::map(Trip::find()->all(), 'id', 'name')
            ); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">

        </div>
    </div>

    <div class="row">
        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'places_count')->textInput() ?>
        </div>

        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'student_count')->textInput() ?>
        </div>

        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'child_count')->textInput() ?>
        </div>

        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'bag_count')->textInput() ?>
        </div>

        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'suitcase_count')->textInput() ?>
        </div>

        <div class="col-sm-2 form-group form-group-sm">
            <?= $form->field($model, 'oversized_count')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">

        </div>

        <div class="col-sm-4 form-group form-group-sm">

        </div>

        <div class="col-sm-4 form-group form-group-sm">

        </div>
    </div>


    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?php
            if($model->time_sat > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/i', $model->time_sat)) {
                $model->time_sat = date("d.m.Y H:i", $model->time_sat);
            }
            echo $form->field($model, 'time_sat')->widget(DateTimePicker::classname(), [
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:i',
                    'autoclose' => true
                ]
            ]); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php
            if($model->time_confirm > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/i', $model->time_confirm)) {
                $model->time_confirm = date("d.m.Y H:i", $model->time_confirm);
            }
            echo $form->field($model, 'time_confirm')->widget(DateTimePicker::classname(), [
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:i',
                    'autoclose' => true
                ]
            ]); ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">

        </div>
    </div>

    <?= $form->field($model, 'comment')->textarea(['rows' => 2]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
