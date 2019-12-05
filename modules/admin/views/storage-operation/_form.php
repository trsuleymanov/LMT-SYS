<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StorageOperation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="storage-operation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'storage_id')->textInput() ?>

    <?= $form->field($model, 'storage_detail_id')->textInput() ?>

    <?= $form->field($model, 'model_id')->textInput() ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'transport_id')->textInput() ?>

    <?= $form->field($model, 'driver_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'operation_type_id')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
