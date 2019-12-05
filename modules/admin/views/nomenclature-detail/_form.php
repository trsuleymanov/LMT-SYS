<?php

use app\models\DetailName;
use app\models\NomenclatureDetail;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="nomenclature-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'detail_name_id')->dropDownList(ArrayHelper::map(DetailName::find()->all(), 'id', 'name')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 form-group form-group-sm">
            <?= $form->field($model, 'comment')->textarea(['rows' => 4]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
