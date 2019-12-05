<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransportExpenses */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-expenses-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'transport_waybill_id')->textInput() ?>

    <?= $form->field($model, 'doc_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expenses_doc_type_id')->textInput() ?>

    <?= $form->field($model, 'expenses_type_id')->textInput() ?>

    <?= $form->field($model, 'expenses_seller_type_id')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'check_attached')->textInput() ?>

    <?= $form->field($model, 'expenses_seller_id')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'points')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expenses_is_taken')->textInput() ?>

    <?= $form->field($model, 'expenses_is_taken_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_method_id')->textInput() ?>

    <?= $form->field($model, 'need_pay_date')->textInput() ?>

    <?= $form->field($model, 'payment_date')->textInput() ?>

    <?= $form->field($model, 'transport_expenses_paymenter_id')->textInput() ?>

    <?= $form->field($model, 'payment_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'creator_id')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updator_id')->textInput() ?>

    <?= $form->field($model, 'view_group')->dropDownList([ 'typical_expenses' => 'Typical expenses', 'other_expenses' => 'Other expenses', 'incoming_payment_requests' => 'Incoming payment requests', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
