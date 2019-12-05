<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\City;

//echo "model:<pre>"; print_r($model); echo "</pre>";
?>

<div class="point-form">

    <?php $form = ActiveForm::begin([
        'id' => 'point-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
    ]); ?>

    <?= $form->field($model, 'city_id')->hiddenInput()->label(false); ?>

    <?php /*
    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'city_id')->dropDownList(ArrayHelper::map(City::find()->all(), 'id', 'name'), []); ?>
        </div>
    </div>
    */ ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'point_of_arrival')->checkbox() ?>
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>

        <div class="col-sm-6 form-group form-group-sm">
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
