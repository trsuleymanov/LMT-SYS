<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallAppeal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-appeal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'type')->dropDownList([ 'administrative_request' => 'Administrative request', 'information_request' => 'Information request', 'operation_with_order' => 'Operation with order', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'contact_id')->textInput() ?>

    <?= $form->field($model, 'success_call_id')->textInput() ?>

    <?= $form->field($model, 'client_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'operator_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
