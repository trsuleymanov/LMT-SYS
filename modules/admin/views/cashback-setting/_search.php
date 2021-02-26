<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashbackSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cashback-setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'start_date') ?>

    <?= $form->field($model, 'order_accrual_percent') ?>

    <?php /*
    <?= $form->field($model, 'order_penalty_percent') ?>
    */ ?>

    <?php /*
    <?= $form->field($model, 'hours_before_start_trip_for_penalty') ?>
    */ ?>
    <?php // echo $form->field($model, 'with_commercial_trips') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
