<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

$this->registerJsFile('js/admin/change-date-trips-orders.js', ['depends'=>'app\assets\AppAsset']);
?>

<div class="change-date-trips-orders-form">

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label>Дата с которой начнется самый первый заказ</label>
            <?= DatePicker::widget([
                'id' => 'date-trips-orders',
                'name'  => 'from_date',
                'value'  => '',
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сдвинуть время', ['id' => 'change-date-trips-orders', 'class' => 'btn btn-success']) ?>
    </div>
</div>
