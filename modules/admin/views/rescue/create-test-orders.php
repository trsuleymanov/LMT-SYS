<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

$this->registerJsFile('js/admin/create-test-orders.js', ['depends'=>'app\assets\AppAsset']);
?>


<div class="create-test-orders-form">

    <?php if (Yii::$app->session->hasFlash('successResult')){ ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('successResult') ?>
        </div>
    <?php } ?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?php
                if($model->date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $model->date)) {
                    $model->date = date("d.m.Y", $model->date);
                }
                echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ],
                    //'options' => ['disabled' => true]
                ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'direction_id')->dropDownList([''] + ArrayHelper::map(\app\models\Direction::find()->all(), 'id', 'sh_name')); ?>
        </div>
    </div>

    <!-- Рейс нужно показывать только если выбрана дата и направление, иначе рейсов будет слишком много! -->
    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'trip_id')->dropDownList([''])->label('Рейс (выберите направление)'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'orders_count')->textInput(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 form-group form-group-sm">
            <?= $form->field($model, 'transports_count')->textInput(); ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Создать заказы', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

