<?php

use app\models\City;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
?>

<div class="yandex-point-form">

    <?php $form = ActiveForm::begin([
        'id' => 'yandex-point-form',
    ]); ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true])->label('Алиас') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?php
//            echo $form->field($model, 'categories_list')
//                ->dropDownList($model->CategoryDropdown,
//                [
//                    // 'class'=>'chosen-select input-md required',
//                    'multiple'=>'multiple'
//                ]
//            )->label("Категории");


            echo $form->field($model, 'categories_list')
                ->checkboxList($model->CategoryDropdown, ['separator' => '<br>'])
                ->label("Категории");

            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'active')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'external_use')->checkbox() ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'popular_departure_point')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'popular_arrival_point')->checkbox() ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'city_id')->dropDownList(
                ArrayHelper::map(City::find()->all(), 'id', 'name')
            ); ?>
        </div>
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'long')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'critical_point')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'point_of_arrival')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8 form-group form-group-sm">
            <?= $form->field($model, 'super_tariff_used')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'description')->textarea(['rows' => 3]); ?>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'time_to_get_together_short')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'time_to_get_together_long')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'point_from_standart_price_diff')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'point_from_commercial_price_diff')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'point_to_standart_price_diff')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'point_to_commercial_price_diff')->textInput(['maxlength' => true]) ?>
        </div>
    </div>




    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
