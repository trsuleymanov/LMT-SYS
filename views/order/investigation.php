<?php
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('js/site/order-cancel-investigation.js', ['depends'=>'app\assets\AppAsset']);
?>

<h3>Расследование по отмененному заказу на рейсе</h3>
<br />

<?= $direction->sh_name ?> <?= $trip->name ?>, <?= date('d/m/Y', $trip->date) ?>
<br />
ФИО: <?= $client->name ?>, <?= $order->places_count ?>М, <?= $order->student_count ?>С, <?= $order->child_count ?>Д
<br /><br />
ВПЗ: <?= ($order->first_writedown_click_time > 0 ? date('d.m.Y H:i', $order->first_writedown_click_time) : '') ?>
<br />
Время отмены: <?= date('d.m.Y H:i', $order->cancellation_click_time) ?>

<br />
<br />
<br />

<?php
$form = ActiveForm::begin([
    'id' => 'order-cancel-investigation-form',
    'options' => [
        'investigation-id' => $investigation->id,
    ],
]);


echo Html::activeHiddenInput($investigation, 'order_id');
echo Html::activeHiddenInput($investigation, 'trip_id');
echo Html::activeHiddenInput($investigation, 'client_id');

if( Yii::$app->session->hasFlash('success') ) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php
}
?>
<div class="row">
    <div class="col-md-3">
        <?php
        if(!empty($investigation->data)) {
            $investigation->data = date("d.m.Y", $investigation->data);
        }
        echo $form->field($investigation, 'data')->widget(DatePicker::classname(), [
            'removeButton' => false,
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy',
                'todayHighlight' => true,
                'autoclose' => true,
            ],
            'options' => []
        ]);
        ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($investigation, 'rejection_reason')->textarea(['rows' => 8]); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($investigation, 'how_client_left')->textarea(['rows' => 8]); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($investigation, 'complaints_and_wishes')->textarea(['rows' => 8]); ?>
    </div>
</div>

<br />
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>

<?php
ActiveForm::end();
?>