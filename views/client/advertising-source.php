<?php

use app\models\AdvertisingSource;
use app\models\AdvertisingSourceReport;
use app\models\City;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<div class="advertising-source-form">

    <p>Как вы узнали о нас?</p>

    <?php
    $form = ActiveForm::begin([
        'id' => 'advertising-source-form',
    ]);
    ?>


    <input type="hidden" name="AdvertisingSourceReport[phone]" value="<?= $phone ?>" />

    <div class="row">
        <div class="col-sm-10">
            <?= $form->field($model, 'advertising_source_id')->dropDownList(ArrayHelper::map(AdvertisingSource::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'))->label(false); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2 first-col" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Ок', ['id' => 'advertising-source-submit', 'class' => 'btn btn-success',]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>