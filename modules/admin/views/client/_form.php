<?php

use app\models\DoTariff;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Point;

?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'do_tariff_id')->dropDownList(
                ['' => ''] + ArrayHelper::map(DoTariff::find()->where(['tariff_type' => 'client'])->all(), 'id', 'description')
            ); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'mobile_phone')
                ->textInput(['maxlength' => true])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'home_phone')
                ->textInput(['maxlength' => true])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'alt_phone')
                ->textInput(['maxlength' => true])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'rating')->textInput(['disabled' => 'disabled']) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php //= $form->field($model, 'sended_orders_places_count')->textInput(['disabled' => 'disabled']) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'sended_prize_trip_count')->textInput(['disabled' => 'disabled']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
