<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="do-tariff-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>

        <?php /*
        <div class="col-sm-3 form-group form-group-sm">
            <?php if($model->isNewRecord) { ?>
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            <?php }else { ?>
                <?= $form->field($model, 'code')->textInput([
                    //'options' => ['disabled' => true]
                    'disabled' => true
                ]) ?>
            <?php } ?>
        </div>*/ ?>

        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'tariff_type')->dropDownList(['order' => 'Для заказа', 'client' => 'Для клиента'], ['prompt' => '']) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <?= $form->field($model, 'use_client_do_tariff')->checkbox() ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'place_price_formula')->textarea(['rows' => 3]); ?>
        </div>
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'order_comment')->textarea(['rows' => 3]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'use_fix_price')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'order_price_formula')->textarea(['rows' => 3]); ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
