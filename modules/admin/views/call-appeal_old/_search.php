<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallAppealSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-appeal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'contact_id') ?>

    <?= $form->field($model, 'success_call_id') ?>

    <?php // echo $form->field($model, 'client_phone') ?>

    <?php // echo $form->field($model, 'operator_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
