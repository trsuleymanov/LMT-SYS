<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки';
?>

<div class="setting">

    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <p style="color:red;">Ссылка на фото-сервер больше не используется в приложении. И нельзя рисунки лежащие на ftp-сервере открыть/увидеть по http (т.е. нельзя открыть в браузере рисунок по адресу http://сервер/пут_к_рисунку). А если нужно выносить параметры подключения к ftp-серверу наружу, то нужно выносить адрес сервера, логин и пароль.</p>
            <?= $form->field($model, 'photo_server_url')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
