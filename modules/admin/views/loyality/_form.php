<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Loyality */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loyality-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'past_sent_orders')->textInput() ?>

    <?= $form->field($model, 'past_sent_orders_places')->textInput() ?>

    <?= $form->field($model, 'past_canceled_orders')->textInput() ?>

    <?= $form->field($model, 'past_canceled_orders_places')->textInput() ?>

    <?= $form->field($model, 'past_fixed_price_orders_places')->textInput() ?>

    <?= $form->field($model, 'past_parcels')->textInput() ?>

    <?= $form->field($model, 'past_is_not_places')->textInput() ?>

    <?= $form->field($model, 'past_prize_trip_count')->textInput() ?>

    <?= $form->field($model, 'past_penalty')->textInput() ?>

    <?= $form->field($model, 'present_sent_orders')->textInput() ?>

    <?= $form->field($model, 'present_sent_orders_places')->textInput() ?>

    <?= $form->field($model, 'present_canceled_orders')->textInput() ?>

    <?= $form->field($model, 'present_canceled_orders_places')->textInput() ?>

    <?= $form->field($model, 'present_fixed_price_orders_places')->textInput() ?>

    <?= $form->field($model, 'present_parcels')->textInput() ?>

    <?= $form->field($model, 'present_is_not_places')->textInput() ?>

    <?= $form->field($model, 'present_prize_trip_count')->textInput() ?>

    <?= $form->field($model, 'present_penalty')->textInput() ?>

    <?= $form->field($model, 'total_sent_orders')->textInput() ?>

    <?= $form->field($model, 'total_sent_orders_places')->textInput() ?>

    <?= $form->field($model, 'total_canceled_orders')->textInput() ?>

    <?= $form->field($model, 'total_canceled_orders_places')->textInput() ?>

    <?= $form->field($model, 'total_fixed_price_orders_places')->textInput() ?>

    <?= $form->field($model, 'total_parcels')->textInput() ?>

    <?= $form->field($model, 'total_is_not_places')->textInput() ?>

    <?= $form->field($model, 'total_prize_trip_count')->textInput() ?>

    <?= $form->field($model, 'total_penalty')->textInput() ?>

    <?= $form->field($model, 'i1')->textInput() ?>

    <?= $form->field($model, 'i2')->textInput() ?>

    <?= $form->field($model, 'i3')->textInput() ?>

    <?= $form->field($model, 'i4')->textInput() ?>

    <?= $form->field($model, 'i5')->textInput() ?>

    <?= $form->field($model, 'loyalty_indicator')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
