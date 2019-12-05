<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OperatorBeelineSubscriptionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="operator-beeline-subscription-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'operator_id') ?>

    <?= $form->field($model, 'subscription_id') ?>

    <?= $form->field($model, 'mobile_ats_login') ?>

    <?= $form->field($model, 'expire_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
