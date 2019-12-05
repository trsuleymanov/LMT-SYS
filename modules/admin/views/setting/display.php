<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки';
?>

<div class="setting">

    <br />
    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'create_orders_yesterday')->checkbox() ?>

    <?= $form->field($model, 'show_short_clients_phones')->checkbox() ?>

    <?= $form->field($model, 'show_short_drivers_phones')->checkbox() ?>

    <?= $form->field($model, 'access_to_client_info_main_page')->checkbox() ?>

    <?= $form->field($model, 'use_mobile_app_by_default')->checkbox() ?>

    <?= $form->field($model, 'show_passenger_button_in_trip_orders_page')->checkbox() ?>

    <br />
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
