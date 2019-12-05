<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Другие настройки';
?>

<div class="setting">

    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'show_short_clients_phones')->checkbox() ?>

    <?php //echo $form->field($model, 'show_short_drivers_phones')->checkbox() ?>

    <?php //echo $form->field($model, 'access_to_client_info_main_page')->checkbox() ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'missed_calls_close_interval')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-9 form-group form-group-sm">
            <?= $form->field($model, 'crm_url_for_beeline_ats')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'min_talk_time_to_perform_request')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'count_hours_before_trip_to_cancel_order')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'interval_to_close_trip')->textInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>