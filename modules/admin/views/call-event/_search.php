<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallEventSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'call_id') ?>

    <?= $form->field($model, 'operator_sip') ?>

    <?= $form->field($model, 'operator_user_id') ?>

    <?= $form->field($model, 'event') ?>

    <?php // echo $form->field($model, 'event_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
