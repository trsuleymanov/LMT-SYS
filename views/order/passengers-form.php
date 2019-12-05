<?php

use app\models\OrderPassenger;
use app\models\Passenger;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\date\DatePicker;
use yii\helpers\ArrayHelper;


$places_count = $order->places_count;
$aOrderPassengers = [];

//echo "<pre>"; print_r($order); echo "</pre>";
//exit;

// пассажиры которые были ранее записаны в базу
$order_passengers = OrderPassenger::find()
    ->where(['order_id' => $order->id])
    ->limit($order->places_count)
    ->all();
$aPassengers = [];

//echo "<pre>"; print_r($order_passengers); echo "</pre>";

if(count($order_passengers) > 0) {
    $passengers = Passenger::find()
        ->where(['id' => ArrayHelper::map($order_passengers, 'passenger_id', 'passenger_id')])
        ->all();
    $aPassengers = ArrayHelper::index($passengers, 'id');
    // echo "<pre>"; print_r($aPassengers); echo "</pre>";
    foreach ($order_passengers as $order_passenger) {
        $aOrderPassengers[] = [
            'order_passenger' => $order_passenger,
            'passenger' => $aPassengers[$order_passenger->passenger_id]
        ];
    }
}


// пустые пассажиры создаем по количеству незаполненных мест
if(count($order_passengers) < $places_count) {
    for ($i = count($order_passengers); $i < $places_count; $i++) {

        if($i == 0) { // если создается пассажир первый в списке пассажиров, значит это клиент заказа

            $order_passenger = new OrderPassenger();
            $passenger = new Passenger();

            $order_passenger->order_id = $order->id;

            $client = $order->client;
            if($client != null) {

                //$order_passenger->client_id = $order->client_id;

                // попробуем найти пассажира соответствующего этому клиенту
                $passenger = Passenger::find()->where(['client_id' => $client->id])->one();
                if($passenger == null) { // если пассажир не найден, то извлечем полезные данные из клиента

                    $passenger = new Passenger();
                    $passenger->client_id = $order->client_id;

//                    $aFio = explode(' ', $client->name);
//                    $passenger->surname = $aFio[0];
//                    if (isset($aFio[1])) {
//                        $passenger->name = $aFio[1];
//                    }
//                    if (isset($aFio[2])) {
//                        $passenger->patronymic = $aFio[2];
//                    }

                    $passenger->fio = $client->name;

                }else {
                    $order_passenger->passenger_id = $passenger->id;
                }
            }

            $aOrderPassengers[] = [
                'order_passenger' => $order_passenger,
                'passenger' => $passenger
            ];

        }else {
            $order_passenger = new OrderPassenger();
            $order_passenger->order_id = $order->id;

            $aOrderPassengers[] = [
                'order_passenger' => $order_passenger,
                'passenger' => new Passenger()
            ];
        }
    }
}


//- серия паспорта (не уникальна) - 4 символов
//- номер паспорта (не уникален) - 6 символа
//Серия+номер - уникальны
//- фамилия
//- имя
//- отчество
//- дата рождения - int
//- гражданство - сделаю новую схему: это будет строка, но в модели будет список возможных вариантов для автозаполнения
//- пол - boolean = 1/0
?>

<div class="passengers-form">

    <?php
    $i = 0;
    foreach($aOrderPassengers as $aOrderPassengerModels) {

        $order_passenger = $aOrderPassengerModels['order_passenger'];
        $passenger = $aOrderPassengerModels['passenger'];
    ?>

        <div class="row">

            <?php $form = ActiveForm::begin([
                'action' => '#',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'options' => [
                    'order-passenger-id' => $order_passenger->id,
                    'order-passenger-order-id' => $order_passenger->order_id,
                    //'passenger-client-id' => $order_passenger->client_id,
                    'order-passenger-passenger-id' => $order_passenger->passenger_id,
                ],
            ]); ?>

            <?= Html::activeHiddenInput($passenger, 'client_id') ?>


            <?php /*
            <div class="col-sm-1 form-group" style="padding-left: 15px;">
                <label class="label-vertical">Ребенок</label>
                <?= $form->field($passenger, 'child')->checkbox(['label' => null])->label(false); ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">Серия</label>
                <?= $form->field($passenger, 'series')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => '1234'
                    ])->label(false);
//                    ->widget(\yii\widgets\MaskedInput::class, [
//                        'mask' => '1234',
//                        'options' => [
//                            'class' => 'form-control'
//                        ],
//                        'clientOptions' => [
//                            'placeholder' => '*',
//                        ]
//                    ]);
                ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">Номер</label>
                <?= $form->field($passenger, 'number')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => '123456'
                    ])->label(false);
//                    ->widget(\yii\widgets\MaskedInput::class, [
//                        'mask' => '123456',
//                        'options' => [
//                            'class' => 'form-control'
//                        ],
//                        'clientOptions' => [
//                            'placeholder' => '*',
//                        ]
//                    ]);
                ?>
            </div>
            */ ?>

            <div class="col-sm-2 form-group" style="padding-left: 15px;">
                <?php //= $form->field($passenger, 'document_type')->dropDownList(Passenger::getDocumentTypes()); ?>

                <label class="label-vertical">Документ</label>
                <select id="passenger-document_type" class="form-control" name="Passenger[document_type]">
                    <?php foreach(Passenger::getDocumentTypes() as $key => $value) { ?>
                        <option value="<?= $key ?>" series_number_placeholder="<?= Passenger::getDocumentTypesPlaceholders()[$key] ?>" ><?= $value ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-1 form-group" style="display: none;">
                <label class="label-vertical">Гражданство</label>
                <?= $form->field($passenger, 'citizenship')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Россия'
                    ])->label(false);
                ?>
            </div>

            <div class="col-sm-2 form-group">
                <label class="label-vertical">Серия и номер</label>
                <?= $form->field($passenger, 'series_number')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => Passenger::getDocumentTypesPlaceholders()['passport']
                    ])->label(false);
                ?>
            </div>

            <?php /*
            <div class="col-sm-1 form-group">
                <label class="label-vertical">Фамилия</label>
                <?= $form->field($passenger, 'surname')
                    ->textInput([
                        'class' => 'form-control',
                    ])->label(false);
                ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">Имя</label>
                <?= $form->field($passenger, 'name')
                    ->textInput(['class' => 'form-control'])->label(false);
                ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">Отчество</label>
                <?= $form->field($passenger, 'patronymic')
                    ->textInput(['class' => 'form-control'])->label(false);
                ?>
            </div>
            */ ?>

            <div class="col-sm-3 form-group">
                <label class="label-vertical">ФИО</label>
                <?= $form->field($passenger, 'fio')->textInput(['class' => 'form-control'])->label(false);
                ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">День Рож-я</label>
                <?php
                if($passenger->date_of_birth > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $passenger->date_of_birth)) {
                    $passenger->date_of_birth = date("d.m.Y", $passenger->date_of_birth);
                }
                echo $form->field($passenger, 'date_of_birth', ['errorOptions' => ['style' => 'display:none;']])
                    ->widget(kartik\date\DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'class' => ''
                        ],
                        'options' => [
                            'id' => 'date_of_birth_' . $i,
                        ]
                    ])
//                    ->widget(\yii\widgets\MaskedInput::class, [
//                        'clientOptions' => [
//                            'alias' =>  'dd.mm.yyyy',
//                        ],
//                        'options' => [
//                            'id' => 'date',
//                            'start-date' => $passenger->date_of_birth,
//                            'class' => 'form-control',
//                            'style' => 'width: 80px;',
//                            //'aria-required' => 'true',
//                            //'placeholder' => '10.05.2017'
//                        ]
//                    ])
                    ->label(false);
                ?>
            </div>

            <div class="col-sm-1 form-group">
                <label class="label-vertical">Пол</label>
                <?php
//                $form->field($passenger, 'gender')
//                    ->textInput(['class' => 'form-control'])->label(false);
                ?>
                <?= $form->field($passenger, 'gender')->dropDownList(['' => ''] + Passenger::getGenders())->label(false); ?>
            </div>


            <div class="col-sm-1" style="margin-top: 30px;">
                <div class="form-group">
                    <?= Html::button(isset($order_passenger->id) ? 'Переписать' : 'Записать', ['class' => $order_passenger->isNewRecord ? 'btn btn-success save-passenger-button' : 'btn btn-primary save-passenger-button', 'style' => 'padding: 3px 2px;  width: 100%;']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    <?php

        $i++;
    } ?>

</div>
