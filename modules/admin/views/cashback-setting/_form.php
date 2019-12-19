<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('js/admin/cashback-setting.js', ['depends'=>'app\assets\AppAsset']);

$start_date = (time() > $model->start_date ? time() : $model->start_date);


if($model->id > 0) {
    $options = [
        'disabled' => 'disabled'
    ];
}else {
    $options = [];
}
?>

<div id="cashback-setting-form" class="box box-solid">

    <div class="box-body">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-sm-12 form-group form-group-sm">
                <?= $form->field($model, 'has_cashback_for_prepayment')->checkbox()->label(false) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 form-group form-group-sm">
                <?= $form->field($model, 'has_cashback_for_nonprepayment')->checkbox()->label(false) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 form-group form-group-sm">
                <?= $form->field($model, 'with_commercial_trips')->checkbox($options)->label(false) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 form-group form-group-sm">
                <?php
                if($model->start_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->start_date)) {
                    $model->start_date = date("d.m.Y", $model->start_date);
                }
                echo $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname(), [
                    'removeButton' => false,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ],
                    'options' => $options
                ]);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'order_accrual_percent')->textInput($options) ?>
            </div>
        </div>

        <?php /*
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'order_penalty_percent')->textInput($options) ?>
            </div>
        </div>*/ ?>

        <?php /*
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'hours_before_start_trip_for_penalty')->textInput($options) ?>
            </div>
        </div>*/ ?>

        <hr />
        <label style="color: red;">Красная зона:</label>
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                Диапозон штрафного кэш-бэка от 0 секунд до <?= $form->field($model, 'red_penalty_max_time')->textInput() ?>
            </div>
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'order_red_penalty_percent')->textInput()->label('Процент штрафа от стоимости заказа для красной зоны (от 0 до 100)') ?>
            </div>
        </div>

        <hr />
        <label style="color: yellow;">Желтая зона:</label>
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                Диапозон штрафного кэш-бэка от <span id="red-penalty-max-time"><?= intval($model->red_penalty_max_time) ?></span> секунд до <?= $form->field($model, 'yellow_penalty_max_time')->textInput() ?>
            </div>
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'order_yellow_penalty_percent')->textInput()->label('Процент штрафа от стоимости заказа для желтой зоны (от 0 до 100)') ?>
            </div>
        </div>

        <hr />
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'max_time_confirm_delta')->textInput()->label('Допустимое колебание ВРПТ (гамма), сек') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'max_time_confirm_diff')->textInput()->label('Допустимое время действия по заказу, сек') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
