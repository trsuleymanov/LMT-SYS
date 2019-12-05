<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
?>

<div class="yandex-point-form">

    <?php $form = ActiveForm::begin([
        'id' => 'yandex-point-form',
        //'enableAjaxValidation' => false,
        //'enableClientValidation' => true,
    ]); ?>

    <?= $form->field($model, 'city_id')->hiddenInput()->label(false); ?>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'long')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'critical_point')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
