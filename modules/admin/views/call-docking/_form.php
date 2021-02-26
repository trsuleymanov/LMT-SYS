<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CallDocking */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-docking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'call_id')->textInput() ?>

    <?= $form->field($model, 'case_id')->textInput() ?>

    <?= $form->field($model, 'conformity')->textInput() ?>

    <?= $form->field($model, 'click_event')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
