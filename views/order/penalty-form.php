<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<div class="penalty-form">

    <?php $form = ActiveForm::begin([
        'id' => 'penalty-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'method' => 'post',
        'options' => [
            'order-id' => $order->id,
        ],
    ]); ?>

    <?= $form->field($order, 'penalty_comment')->textarea(['rows' => 2]); ?>

    <div class="row">
        <div class="col-sm-3 form-group">
            <div class="form-group">
                <?= Html::button('Отменить', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
        <div class="col-sm-2 form-group">
            <div class="form-group">
                <?= Html::submitButton('Отштафовать', ['class' => 'btn btn-success', ]) ?>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
