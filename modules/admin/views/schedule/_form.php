<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Расписание
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'direction_id')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-sm-6 form-group">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-3 form-group">
                <?php
                if($model->start_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->start_date)) {
                    $model->start_date = date("d.m.Y", $model->start_date);
                }
                echo $form->field($model, 'start_date')->widget(kartik\date\DatePicker::classname(), [
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ]
                ]);
                ?>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
if(!$model->isNewRecord)
{ ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Рейсы расписания
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">

        <a href="<?= Url::to(['/admin/schedule-trip/create', 'schedule_id' => $model->id]) ?>" class="btn btn-mini btn-success" type="submit">Добавить рейс</a>

        <?= $this->render('index', [
            'searchTripModel' => $searchTripModel,
            'dataTripProvider' => $dataTripProvider,
            'edit_delete_trips' => true
        ]); ?>

    </div>
</div>

<?php
}
?>