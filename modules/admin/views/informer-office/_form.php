<?php

use app\models\DoTariff;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InformerOffice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="informer-office-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php //if ($model->isNewRecord) { ?>
        <div class="row">
            <div class="col-sm-6 form-group form-group-sm">
                <?= $form->field($model, 'cashless_payment')->checkbox() ?>
            </div>
        </div>
    <?php //} ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'do_tariff_id')->dropDownList(
                ['' => ''] + ArrayHelper::map(DoTariff::find()->where(['tariff_type' => 'order'])->all(), 'id', 'description')
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
