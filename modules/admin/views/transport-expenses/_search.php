<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransportExpensesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-expenses-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'transport_waybill_id') ?>

    <?= $form->field($model, 'doc_number') ?>

    <?= $form->field($model, 'expenses_doc_type_id') ?>

    <?= $form->field($model, 'expenses_type_id') ?>

    <?php // echo $form->field($model, 'expenses_seller_type_id') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'check_attached') ?>

    <?php // echo $form->field($model, 'expenses_seller_id') ?>

    <?php // echo $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'points') ?>

    <?php // echo $form->field($model, 'expenses_is_taken') ?>

    <?php // echo $form->field($model, 'expenses_is_taken_comment') ?>

    <?php // echo $form->field($model, 'payment_method_id') ?>

    <?php // echo $form->field($model, 'need_pay_date') ?>

    <?php // echo $form->field($model, 'payment_date') ?>

    <?php // echo $form->field($model, 'transport_expenses_paymenter_id') ?>

    <?php // echo $form->field($model, 'payment_comment') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'creator_id') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updator_id') ?>

    <?php // echo $form->field($model, 'view_group') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
