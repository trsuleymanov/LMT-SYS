<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallCase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-case-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'case_type')->dropDownList([ 'administrative_request' => 'Administrative request', 'information_request' => 'Information request', 'operation_with_order' => 'Operation with order', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'open_time')->textInput() ?>

    <?= $form->field($model, 'call_count')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'not_completed' => 'Not completed', 'adm_completed' => 'Adm completed', 'inf_completed' => 'Inf completed', 'missed_completed' => 'Missed completed', 'input_call_missed_completed' => 'Input call missed completed', 'output_call_missed_completed' => 'Output call missed completed', 'auto_completed' => 'Auto completed', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'close_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
