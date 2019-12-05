<?php
use app\models\TransportExpensesDocType;
use app\models\TransportExpensesTypes;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

// {Вид_ДО} of {ДО} от {Дата ДО} по т/с {фиксировано согласно путевому листу} на сумму {Общая сумма по Работы/Услуги+Запчасти/Товары}

// Вид_ДО - TransportExpenses - expenses_doc_type_id
// ДО - TransportExpenses - expenses_type_id
// Дата ДО - TransportExpenses - need_pay_date
// т/с - transport_id
// {Общая сумма по Работы/Услуги+Запчасти/Товары} - считать по детализации и обновлять с помощью javascript
//if ($tr_expense['expenses_doc_type_id'] > 0) {
//
//}

$price = 0;
foreach($detailings as $detailing) {
    $price += doubleval($detailing->price);
}
?>

<?php $form = ActiveForm::begin([
    'id' => 'detailing-title-form',
    'options' => [
        //'transport-waybill-id' => $model->id,
        'transport_expenses_id' => $tr_expenses->id,
        'style' => 'position:absolute; padding: 0 15px; width: 760px; left: 0;'
    ],
]); ?>
<?php
echo $form
    ->field($tr_expenses, 'expenses_doc_type_id', [
        'errorOptions' => ['style' => 'display:none;'],
        'options' => [
            'style' => 'display: inline-block; '
        ]
    ])
    ->dropDownList(
        [0 => ''] + ArrayHelper::map(TransportExpensesDocType::find()->all(), 'id', 'name'),
        [
            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_doc_type_id]',
            'id' => 'transportexpenses-expenses_doc_type_id-'.$tr_expenses->id,
            'class' => 'transportexpenses-expenses_doc_type_id form-control',
            'style' => 'width: 140px; ',
        ]
    )->label(false);
?>
<?php
echo $form
    ->field($tr_expenses, 'expenses_type_id', [
        'errorOptions' => ['style' => 'display:none;'],
        'options' => [
            'style' => 'display: inline-block; margin-left: 10px;'
        ]
    ])
    ->dropDownList(
        [0 => ''] + ArrayHelper::map(TransportExpensesTypes::find()->all(), 'id', 'name'),
        [
            'name' => 'TransportExpenses['.$tr_expenses->id.'][expenses_type_id]',
            'id' => 'transportexpenses-expenses_type_id-'.$tr_expenses->id,
            'class' => 'transportexpenses-expenses_type_id form-control',
            'style' => 'width: 140px; display: inline-block; margin-left: 5px; margin-right: 5px;'
        ]
    )->label(false);
?>
 № <?= $form->field($tr_expenses, 'doc_number', [
    'errorOptions' => [
        'style' => 'display:none;',
    ],
    'options' => [
        'style' => 'display: inline-block;'
    ]
])->textInput([
    'maxlength' => true,
    'name' => 'TransportExpenses['.$tr_expenses->id.'][doc_number]',
    'id' => 'transportexpenses-doc_number-'.$tr_expenses->id,
    'class' => 'transportexpenses-doc_number form-control',
    'style' => 'width: 100px; display: inline-block; margin-left: 5px; margin-right: 5px;'
    //'style' => 'width: 60px;'
])->label(false);
?>
 от <?php
if($tr_expenses->need_pay_date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $tr_expenses->need_pay_date)) {
    $tr_expenses->need_pay_date = date("d.m.Y", $tr_expenses->need_pay_date);
}
echo $form->field($tr_expenses, 'need_pay_date',
    [
        'errorOptions' => ['style' => 'display:none;'],
        'options' => [
            'style' => 'display: inline-block; padding: 0 2px; width: 96px;  margin-left: 5px; margin-right: 5px;',
        ],
        'inputOptions' => [
            'class' => 'form-control transportexpenses-need_pay_date',
            'id' => 'detailing-transportexpenses-need_pay_date-'.$tr_expenses->id,
            'placeholder' => '10.05.2017',
            'name' => 'TransportExpenses['.$tr_expenses->id.'][need_pay_date]',
        ]
    ])
    ->label(false);
?>
<br />по
<span style="margin-left: 5px;">
    <?= ($waybill->transport != null ? $waybill->transport->car_reg_places_count : '') ?>
</span>
на сумму <div id="detailing-title-form-price" style="display: inline-block; "><?= $tr_expenses->price ?></div> руб.
<?php ActiveForm::end(); ?>

