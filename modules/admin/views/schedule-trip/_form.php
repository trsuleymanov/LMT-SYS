<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
?>

<div class="trip-static-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'schedule_id')->hiddenInput()->label(false) ?>

    <div class="row">

        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'start_time')
                    ->widget(TimePicker::className(), [
                        'pluginOptions' => [
                            'showMeridian' => false,
                            'minuteStep' => 10,
                            'defaultTime' => '12:00'
                        ]
                    ])
            ?>
        </div>

        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'mid_time')
                ->widget(TimePicker::className(), [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'minuteStep' => 10,
                        'defaultTime' => '12:00'
                    ]
                ])
            ?>
        </div>

        <div class="col-sm-3 form-group form-group-sm">
            <?= $form->field($model, 'end_time')
                ->widget(TimePicker::className(), [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'minuteStep' => 10,
                        'defaultTime' => '12:00'
                    ]
                ])
            ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
