<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoyalitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loyality-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'past_sent_orders') ?>

    <?= $form->field($model, 'past_sent_orders_places') ?>

    <?= $form->field($model, 'past_canceled_orders') ?>

    <?php // echo $form->field($model, 'past_canceled_orders_places') ?>

    <?php // echo $form->field($model, 'past_fixed_price_orders_places') ?>

    <?php // echo $form->field($model, 'past_parcels') ?>

    <?php // echo $form->field($model, 'past_is_not_places') ?>

    <?php // echo $form->field($model, 'past_prize_trip_count') ?>

    <?php // echo $form->field($model, 'past_penalty') ?>

    <?php // echo $form->field($model, 'present_sent_orders') ?>

    <?php // echo $form->field($model, 'present_sent_orders_places') ?>

    <?php // echo $form->field($model, 'present_canceled_orders') ?>

    <?php // echo $form->field($model, 'present_canceled_orders_places') ?>

    <?php // echo $form->field($model, 'present_fixed_price_orders_places') ?>

    <?php // echo $form->field($model, 'present_parcels') ?>

    <?php // echo $form->field($model, 'present_is_not_places') ?>

    <?php // echo $form->field($model, 'present_prize_trip_count') ?>

    <?php // echo $form->field($model, 'present_penalty') ?>

    <?php // echo $form->field($model, 'total_sent_orders') ?>

    <?php // echo $form->field($model, 'total_sent_orders_places') ?>

    <?php // echo $form->field($model, 'total_canceled_orders') ?>

    <?php // echo $form->field($model, 'total_canceled_orders_places') ?>

    <?php // echo $form->field($model, 'total_fixed_price_orders_places') ?>

    <?php // echo $form->field($model, 'total_parcels') ?>

    <?php // echo $form->field($model, 'total_is_not_places') ?>

    <?php // echo $form->field($model, 'total_prize_trip_count') ?>

    <?php // echo $form->field($model, 'total_penalty') ?>

    <?php // echo $form->field($model, 'i1') ?>

    <?php // echo $form->field($model, 'i2') ?>

    <?php // echo $form->field($model, 'i3') ?>

    <?php // echo $form->field($model, 'i4') ?>

    <?php // echo $form->field($model, 'i5') ?>

    <?php // echo $form->field($model, 'loyalty_indicator') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
