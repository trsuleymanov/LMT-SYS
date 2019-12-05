<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\SelectWidget;
use yii\web\JsExpression;
use app\models\City;
use yii\helpers\ArrayHelper;


//echo 'alias:<pre>'; print_r(Yii::$app->session->get('role_alias')); echo '</pre>';
?>

<div class="transport-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) { ?>
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'regular')->checkbox()->label(false); ?>
            </div>

            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'accountability')->checkbox()->label(false); ?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'sh_model')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?php if($model->base_city_id > 0) { ?>
                <label class="control-label">Город базирования</label>
                <div class="form-control"><label><?= $model->baseCity->name ?></label></div>
            <?php }else { ?>
                <?= $form->field($model, 'base_city_id')->dropDownList(['' => ''] + ArrayHelper::map(City::find()->all(), 'id', 'name')); ?>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'car_reg')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'formula_id')->dropDownList(['' => ''] + ArrayHelper::map(\app\models\Formula::find()->all(), 'id', 'name')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'places_count')->textInput() ?>
        </div>

        <div class="col-sm-4 form-group form-group-sm"></div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
