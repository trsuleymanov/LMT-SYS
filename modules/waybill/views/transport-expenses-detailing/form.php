<?php

use app\models\TransportExpensesDetailing;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


$aWorkDetailings = [];
$aGoodsDetailings = [];
foreach($detailings as $detailing) {
    if($detailing->type == 'work_services') {
        $aWorkDetailings[] = $detailing;
    }else {
        $aGoodsDetailings[] = $detailing;
    }
}


// услуга - это работа, а запчасть и деталь - это товар

// Геометрически:

//Заголовок: Тип_расхода номер_док от дата_док / т/с номер_модель_т/с
//Подзаголовок: Работы/Услуги
//Таблица с тремя столбцами: номер п/п, наименование, стоимость
//Итого по работам/услугам: сумма руб.
//Подзаголовок: Запчасти/товары: номер п/п, наименование, стоимость
//Итого по запчастям/товарам: сумма руб.

/*
// Например:
<b>Заказ-наряд № 9901 от 12.11.2018 по т/с ФД 458<b><br>
<br>

<i>Работы/Услуги:</i><br>
1 Замена масла 300,00<br>
2 Замена топливного филльтра 200,00<br>
<br>
<i>Итого по работам/услугам: 500,00 руб.</i><br>

<br>
<i>Запчасти/Товары</i><br>
1 Топливный фильтр 920,00<br>
<br>
<i>Итого по запчастям/товарам: 920,00 руб.</i><br>
*/

$work_total_price = 0;
$service_total_price = 0;
$total_detailing_key = 0;
?>
<div class="transport-expenses-detailing-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'transport-expenses-detailing-form',
        'options' => [
            'transport-expenses-id' => $tr_expenses->id
        ]
    ]);
    // <td>Наим.док</td><td>Дата</td><td>Наим.</td><td>Цена</td><td>Тип</td><td>закр.крест</td>
    ?>

    <div class="detailings-block" block-type = "work_services">
        <i>Работы/Услуги:</i><br>
        <table class="transport-expenses-detailing-table">
            <tr>
                <td>№ п/п</td>
                <td>Наименование</td>
                <td>Цена</td>
                <td></td>
            </tr>
            <?php

            foreach($aWorkDetailings as $key => $detailing) { ?>
                <?php
                echo $this->render('_row', [
                    'form' => $form,
                    'detailing' => $detailing,
                    'num' => $key + 1,
                    'key' => $total_detailing_key,
                    //'aDetailingTypes' => TransportExpensesDetailing::getWorkTypes()
                ]);

                $work_total_price += floatval($detailing->price);
                $total_detailing_key++;
            } ?>
        </table>
        <br />
        <i>Итого по работам/услугам: <span class="total-price"><?= $work_total_price ?></span> руб.</i><br>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', [''], ['class' => 'btn btn-success add-transport-expenses-detailing']) ?>
    </div>

    <br />

    <div class="detailings-block" block-type='details_goods'>
        <i>Запчасти/Товары:</i><br>
        <table class="transport-expenses-detailing-table">
            <tr>
                <td>№ п/п</td>
                <td>Наименование</td>
                <td>Цена</td>
                <td></td>
            </tr>
            <?php foreach($aGoodsDetailings as $key => $detailing) { ?>
                <?php
                echo $this->render('_row', [
                    'form' => $form,
                    'detailing' => $detailing,
                    'num' => $key + 1,
                    'key' => $total_detailing_key,
                    //'aDetailingTypes' => TransportExpensesDetailing::getGoodTypes()
                ]);

                $service_total_price += floatval($detailing->price);
                $total_detailing_key++;
            } ?>
        </table>
        <br />
        <i>Итого по запчастям/товарам: <span class="total-price"><?= $service_total_price ?></span> руб.</i><br>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i>', [''], ['class' => 'btn btn-success add-transport-expenses-detailing']) ?>


    </div>

    <br /><br />
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>