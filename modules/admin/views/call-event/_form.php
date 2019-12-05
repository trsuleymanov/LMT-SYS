<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'call_id')->textInput() ?>

    <?= $form->field($model, 'operator_sip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'operator_user_id')->textInput() ?>

    <?= $form->field($model, 'event')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'event_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
