<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки пересчета';

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>

<div id="a-vrpt-page">

    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'ya_point_p_AK')->textInput() ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'ya_point_p_KA')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'max_time_short_trip_AK')->textInput() ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'max_time_short_trip_KA')->textInput() ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-2 form-group form-group-sm">
            <?= Html::submitButton('Вначале сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= Html::button('Потом пересчитать', ['class' => 'btn btn-info',  'id' => 'recount-points-time-together']) ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            Потом посмотреть что получилось:
        </div>
        <div class="col-sm-2 form-group form-group-sm">
            <a href="/admin/city/update?id=1" target="_blank">Казань</a>
            <br />
            <a href="/admin/city/update?id=2" target="_blank">Альметьевск</a>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>