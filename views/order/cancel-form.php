<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\OrderCancellationReason;
use yii\helpers\ArrayHelper;
?>


<div class="cancel-order-form">

    <?php $form = ActiveForm::begin([
        'id' => 'cancel-order-form',
    ]);
    ?>


    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($order, 'cancellation_reason_id')
                ->dropDownList(ArrayHelper::map(OrderCancellationReason::find()->all(), 'id', 'name'))
                ->label('Выберите причину отказа'); ?>
        </div>
    </div>

    <br />

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <?= Html::button('Отменить', ['class' => 'btn btn-default', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::submitButton('Применить', ['id' => 'cancel-order-form-submit', 'class' => 'btn btn-success', ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

