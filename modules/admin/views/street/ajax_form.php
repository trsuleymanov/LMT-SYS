<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\City;

?>

<div class="point-form">

    <?php $form = ActiveForm::begin([
        'id' => 'street-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
    ]); ?>

    <?= $form->field($model, 'city_id')->hiddenInput()->label(false); ?>

    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
