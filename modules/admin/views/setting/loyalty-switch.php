<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Переключатель лояльности';
?>

<div class="loyalty-switch">

    <?php if( Yii::$app->session->hasFlash('success') ): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php endif;?>

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <?= $form->field($model, 'loyalty_switch')
                ->radioList([
                    'cash_back_on' => 'Действует система Кэш-бэков',
                    'fifth_place_prize' => 'Действует правило 5-й призовой'
            ])->label(false) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>