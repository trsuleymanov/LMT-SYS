<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransportExpensesDetailing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transport-expenses-detailing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'expense_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'work_services' => 'Work services', 'details_goods' => 'Details goods', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
