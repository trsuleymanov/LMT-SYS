<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Formula;

$formuls = Formula::find()->all();

?>
<div class="calculate-form">

    <?php $form = ActiveForm::begin([
        'id' => 'calculate-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]); ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <div class="form-group">
                <label class="control-label">Аргумент</label>
                <input class="form-control" id="argument" name="argument" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <div class="form-group">
                <label class="control-label">Формула</label>
                <?php
                echo Html::dropDownList('formula', 0, ArrayHelper::map($formuls, 'id', 'name'), ['class' => 'form-control', 'id' => 'formula_id']);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= Html::button('Расчитать', ['class' => 'btn btn-info', 'id' => 'calculate']) ?>
        </div>
    </div>

    <div class="row" id="result" style="display: none;">
        <div class="col-sm-4 form-group form-group-sm">
            Результат: <span id="result-value">123</span>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
