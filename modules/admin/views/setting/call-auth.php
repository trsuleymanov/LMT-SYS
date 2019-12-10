<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'CALL- авторизация';
?>

<div class="call-auth">

    <br />
    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'phone_to_confirm_user')->textInput() ?>
        </div>
    </div>

    <br />
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>