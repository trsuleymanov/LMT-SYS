<?php

use app\models\Transport;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<div class="move-expense-form">

    <input type="hidden" id="move-expense-id" value="<?= $expense->id ?>" />

    <div class="row">
        <div class="col-sm-12">
            <?php
            echo '<label class="control-label">Дата ПЛ</label>';
            echo DatePicker::widget([
                    'id' => 'move-expense-date-to',
                    'name' => 'move-expense-date-to',
                    //'value' => '11.11.2018',
                    'options' => ['placeholder' => 'Выберите дату'],
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose'=>true
                    ]
            ]);
            ?>
        </div>
    </div>

    <br />
    <div class="row">
        <div class="col-sm-12">
            <?php
            echo '<label class="control-label">Транспорт</label>';
            echo Html::dropDownList('move-expense-transport-to', null, [0 => ''] + ArrayHelper::map(Transport::find()->all(), 'id', 'name3'), ['class' => 'form-control', 'id' => 'move-expense-transport-to'])
            ?>
        </div>
    </div>

    <br />
    <div class="row">
        <div class="col-sm-12" id="select-pl-block" style="display: none;">
            <?php
            echo '<label class="control-label">Выберите ПЛ</label>';
            echo Html::dropDownList('move-expense-waybill-to', null, [0 => ''], ['class' => 'form-control', 'id' => 'move-expense-waybill-to'])
            ?>
        </div>
    </div>

    <br />
    <div class="row">
        <div class="col-sm-12" id="submit-button-block" style="display: none;">
            <div class="form-group">
                <?= Html::button('Перенести расход', ['id' => 'move-button', 'class' => 'btn btn-info',]) ?>
            </div>
        </div>
    </div>

</div>
