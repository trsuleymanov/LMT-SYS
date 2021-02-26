<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DriverPhoto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="driver-photo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'time_loading_finish')->textInput() ?>

    <?= $form->field($model, 'photo_created_on_mobile')->textInput() ?>

    <?= $form->field($model, 'photo_link')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
