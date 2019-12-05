<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status_id') ?>

    <?= $form->field($model, 'cancellation_reason_id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'client_id') ?>

    <?php // echo $form->field($model, 'plan_trip_transport_id') ?>

    <?php // echo $form->field($model, 'fact_trip_transport_id') ?>

    <?php // echo $form->field($model, 'point_id_from') ?>

    <?php // echo $form->field($model, 'point_id_to') ?>

    <?php // echo $form->field($model, 'prize_trip_count') ?>

    <?php // echo $form->field($model, 'tmtb_id') ?>

    <?php // echo $form->field($model, 'places_count') ?>

    <?php // echo $form->field($model, 'baggage') ?>

    <?php // echo $form->field($model, 'is_not_places') ?>

    <?php // echo $form->field($model, 'time_confirm') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'contacts') ?>

    <?php // echo $form->field($model, 'reset') ?>

    <?php // echo $form->field($model, 'time_sat') ?>

    <?php // echo $form->field($model, 'use_fix_price') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
