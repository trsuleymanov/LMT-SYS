<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UserRole;
?>

<div class="change-password-form">
    <?php $form = ActiveForm::begin([
        'id' => 'change-password-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-sm-9 form-group form-group-sm">
            <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>