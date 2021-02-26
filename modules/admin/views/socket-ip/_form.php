<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>

<div class="socket-ip-page">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>
        </div>

    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 2]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
