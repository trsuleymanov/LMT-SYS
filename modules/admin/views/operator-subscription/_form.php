<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\SelectWidget;
use yii\web\JsExpression;

?>

<div class="operator-beeline-subscription-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php
    /*
    ?>
    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?php
            echo $form->field($model, 'operator_id')->widget(SelectWidget::className(), [
                'initValueText' => ($model != null && $model->user != null ? $model->user->username : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                ],
                'ajax' => [
                    'url' => '/user/ajax-get-users',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            "get-username": true
                        };
                    }'),
                ],
            ]);
            ?>

        </div>

    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'mobile_ats_login')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($model, 'subscription_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expire_at')->textInput() ?>
    */ ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'mobile_ats_login')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'minutes')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
