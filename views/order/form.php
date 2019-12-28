<?php

use app\models\Call;
use app\models\OrderPassenger;
use app\models\Setting;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Point;
use app\models\Trip;
use yii\web\JsExpression;
use app\models\Direction;
use app\models\InformerOffice;
use yii\helpers\Url;
use app\widgets\SelectWidget;
use kartik\money\MaskMoney;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

$order_passengers_count = 0;
if($order->id > 0) {
    $order_passengers_count = OrderPassenger::find()->where(['order_id' => $order->id])->count();
}

//echo "<pre>"; print_r($order); echo "</pre>";
//if($clear_phones == true) {
//
//    $client->mobile_phone = '';
//    $client->home_phone = '';
//    $client->alt_phone = '';
//    $order->additional_phone_1 = '';
//    $order->additional_phone_2 = '';
//    $order->additional_phone_3 = '';
//}

// $setting = Setting::find()->where(['id' => 1])->one();


$form = ActiveForm::begin([
    'id' => 'order-client-form',
    'action' => ($order->id > 0 ? Url::to(['ajax-update-order', 'id' => $order->id]) : Url::to(['ajax-create-order'])),
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'options' => [
        'order-id' => $order->id,
        'order-temp-identifier' => $order->temp_identifier,
        'order-passengers-count' => $order_passengers_count,
        //'mode' => $mode
    ],
]);
?>



<div class="order-form">


    <input type="hidden" name="Order[relation_order_id]" value="<?= $order->relation_order_id ?>" />
    <input type="hidden" name="Client[email]" value="<?= $client->email ?>" />

    <div class="row">
        <div class="col-sm-1 first-col" style="margin-left: -20px;">
            <label class="label-horizontal">Дата</label>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <div class="form-group field-order-date required">
                <?php
                if($order->date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $order->date)) {
                    $order->date = date("d.m.Y", $order->date);
                }
                echo $form->field($order, 'date', ['errorOptions' => ['style' => 'display:none;']])
                    ->widget(kartik\date\DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'class' => ''
                        ],
                        'options' => [
                            'id' => 'date',
                        ]
                    ])
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'alias' =>  'dd.mm.yyyy',
                        ],
                        'options' => [
                            'id' => 'date',
                            'start-date' => $order->date,
                            'style' => 'width: 80px;',
                            'aria-required' => 'true',
                            'placeholder' => '10.05.2017'
                        ]
                    ])
                    ->label(false);
                ?>
            </div>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">НПР</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($order, 'direction_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList([0 => '---'] + ArrayHelper::map(Direction::find()->all(), 'id', 'sh_name'), [
                        'id' => 'direction',
                        'class' => 'checkbox',
                        'disabled' => empty($order->date),
                    ])
                    ->label(false); ?>
            </div>
        </div>

        <div class="col-sm-4 mini-side-padding nowrap">
            <label class="label-horizontal">Рейс</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php
                if($order->direction_id > 0)
                {
                    $arTrips = ['' => '---'];
                    if(!empty($order->date)) {

                        $trips = Trip::getTripsQuery(strtotime($order->date), $order->direction_id)
                            //->andWhere(['date_sended' => NULL])
                            ->andWhere([
                                'OR',
                                ['date_sended' => NULL],
                                ['id' => $order->trip_id]
                            ])
                            ->all();

                        foreach($trips as $trip) {
                            $arTrips[$trip->id] = $trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')';
                        }
                    }
                    // echo "trip_id_1 = ".$order->trip_id."<br />";
                    echo $form->field($order, 'trip_id', ['errorOptions' => ['style' => 'display:none;']])
                        ->dropDownList(
                            $arTrips,
                            [
                                'id' => 'trip',
                                'class' => 'checkbox',
                                'style' => 'width: 174px;'
                            ]
                        )
                        ->label(false);
                }else {
                    // echo "trip_id_2 = ".$order->trip_id."<br />";
                    echo $form->field($order, 'trip_id', ['errorOptions' => ['style' => 'display:none;']])
                        ->dropDownList(
                            ['' => '---'],
                            [
                                'id' => 'trip',
                                'class' => 'checkbox',
                                'style' => 'width: 180px;',
                                'disabled' => true
                            ])
                        ->label(false);
                }
                ?>
            </div>
        </div>

        <div class="col-sm-3 nowrap" style="margin-left: 16px;">
            <?php /*
            <input id="informer-office-disable" type="checkbox" <?= ($order->informer_office_id > 0 ? 'checked' : '') ?> class="label-horizontal"> <label class="label-horizontal" style="margin-top: 10px;">Ист.</label>
            */ ?>
            <label class="label-horizontal" style="margin-top: 7px;">Ист.</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($order, 'informer_office_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList(ArrayHelper::map(InformerOffice::find()->all(), 'id', 'name'), [
                        'class' => 'checkbox',
                        //'disabled' => empty($order->informer_office_id)
                    ])
                    ->label(false); ?>
            </div>
        </div>
    </div>

    <div class="yellow-line">- Номер с которого звоните?</div>
    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Моб. основной</label>
            <?php
            if($mode == 'view') {

                echo '<input id="client-mobile_phone_view" disabled="disabled" value="'.(!empty($client->mobile_phone) ? Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="client-mobile_phone" style="display: none;" name="Client[mobile_phone]" value="'.$client->mobile_phone.'" type="text">';

                /*
                $real_mobile_phone = $client->mobile_phone;
                if(!empty($client->mobile_phone)) {
                    $mobile_phone = Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones', 'x');
                }
                //echo 'mobile_phone='.$client->mobile_phone; // +7 .......1111 либо +7-111-111-1111
                //$client->mobile_phone = '+7-xxx-xxx-1111';
                echo $form->field($client, 'mobile_phone')
                    ->textInput([])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        //'mask' => '+7-999-999-9999',
                        'mask' => '+7-***-***-****',
                        'options' => [
                            'disabled' => true,
                            'phone' => $real_mobile_phone
                        ],
                        'clientOptions' => [
                            'placeholder' => '*',
                        ],
                    ])
                ;*/
                /*
                echo $form->field($client, 'mobile_phone')
                    ->textInput([])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            'disabled' => true,
                        ],
                        'clientOptions' => [
                            'placeholder' => '*',
                        ],
                    ])
                ;*/


            }else {
                echo $form->field($client, 'mobile_phone')
                    ->textInput([])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            'disabled' => ($order->direction_id > 0 ? false : true),
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*',
                        ]
                    ]);
            }
            ?>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-4" style="padding-left: 5px;">
            <label class="label-vertical">ФИО</label>
            <?php
            if($mode == 'view') {
//                echo $form->field($client, 'name', ['errorOptions' => ['style' => 'display:none;']])
//                    ->textInput([
//                        'class' => 'input-text',
//                        'placeholder' => 'Иванов Иван Иваныч',
//                        'disabled' => true
//                    ])->label(false);

                echo '<input id="client-name_view" disabled="disabled" value="'.$client->name.'" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="client-name" style="display: none;" name="Client[name]" value="'.$client->name.'" type="text">';


            }else {
                echo $form->field($client, 'name', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text',
                        'placeholder' => 'Иванов Иван Иваныч',
                        'disabled' => ($order->direction_id > 0 ? false : true)
                    ])->label(false);
            }
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-2 mini-side-padding">
            <?php
            if($mode == 'view') {
                echo Html::button('Инфо-окно', ['id' => 'info-window', 'class' => 'btn btn-default', 'order-id' => $order->id]);
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Домашний</label>
            <?php
            if($mode == 'view') {

                echo '<input id="client-home_phone_view" disabled="disabled" value="'.(!empty($client->home_phone) ? Setting::changeShowingPhone($client->home_phone, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="client-home_phone" style="display: none;" name="Client[home_phone]" value="'.$client->home_phone.'" type="text">';

                /*
                if(!empty($client->home_phone)) {
                    $client->home_phone = Setting::changeShowingPhone($client->home_phone, 'show_short_clients_phones', 'x');
                }
                echo $form->field($client, 'home_phone')
                    ->textInput([
                        //'class' => 'input-text',
                        //'placeholder' => '8-495-1234567',
                        //'disabled' => true
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-***-***-****',
                        'options' => [
                            'disabled' => true
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
                */

            }else {
                echo $form->field($client, 'home_phone')
                    ->textInput([
                        'class' => 'input-text',
                        //'placeholder' => '8-495-1234567',
                        //'disabled' => true
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            'disabled' => ($order->direction_id > 0 ? false : true),
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
            }
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-4 mini-side-padding">
            <label class="label-vertical">Другой</label>
            <?php
            if($mode == 'view') {

                echo '<input id="client-alt_phone_view" disabled="disabled" value="'.(!empty($client->alt_phone) ? Setting::changeShowingPhone($client->alt_phone, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="client-alt_phone" style="display: none;" name="Client[alt_phone]" value="'.$client->alt_phone.'" type="text">';

                /*
                if(!empty($client->alt_phone)) {
                    $client->alt_phone = Setting::changeShowingPhone($client->alt_phone, 'show_short_clients_phones', 'x');
                }
                echo $form->field($client, 'alt_phone')
                    ->textInput([
                        //'class' => 'input-text',
                        //'disabled' => true,
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-***-***-****',
                        'options' => [
                            //'disabled' => ($order->direction_id > 0 ? false : true),
                            'disabled' => true,
                            'style' => 'width: 100%;',
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
                */

            }else {

                echo $form->field($client, 'alt_phone')
                    ->textInput([
                        //'class' => 'input-text',
                        //'disabled' => true,
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            'disabled' => ($order->direction_id > 0 ? false : true),
                            'style' => 'width: 100%;',
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
            }
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-2 mini-side-padding">
            <?php
            if($mode == 'view') {
                echo Html::button('Ред. руками', ['id' => 'edit-by-hand', 'class' => 'btn btn-default', 'style' => 'padding: 3px 4px;', 'order-id' => $order->id]);
            }
            ?>
        </div>
    </div>



    <div class="yellow-line" style="margin-top: 5px;">- Сколько человек поедет? Есть ли студенты и дети? Будет ли багаж?</div>
    <div class="row">
        <div class="col-sm-1 first-col nowrap">
            <?php
            echo Html::activeCheckbox($order, 'is_not_places', ['label' => '', 'id' => 'places-count-disable', 'style'=>"margin-top: -20px;", 'class' => 'label-horizontal']);
            ?>
            <label class="label-horizontal" style="text-align:left; vertical-align: bottom; white-space: normal; margin-top: 20px; font-size: 11px;">Без места</label>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Мест</label>
            <?= $form->field($order, 'places_count')
                ->textInput([
                    'class' => 'input-text'
                ])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'clientOptions' => [
                        'showMaskOnFocus' => false,
                        'showMaskOnHover' => false
                    ],
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>

            <?php
            $style = "";
            $class = "";
            $text = "";
            if($order->places_count > 0 && $order->places_count == $order_passengers_count) {
                $text = "Изменить";
            }elseif($order->places_count > 0 && $order->places_count > $order_passengers_count) {
                $text = "Добавить";
                $class = "text-danger";
            }elseif($order->places_count > 0 && $order->places_count < $order_passengers_count) {
                $text = "Удалить";
                $class = "text-danger";
            }else {
                $style = "display: none;";
            }
            ?>
            <a href="#" class="edit-passengers <?= $class ?>" order-id="<?= $order->id ?>" client-id="<?= $order->client_id ?>" style="<?= $style ?>"><?= $text ?></a>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Студ.</label>
            <?= $form->field($order, 'student_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Дет.</label>
            <?= $form->field($order, 'child_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Сумки</label>
            <?= $form->field($order, 'bag_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Чемод.</label>
            <?= $form->field($order, 'suitcase_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Негабариты</label>
            <?= $form->field($order, 'oversized_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding nowrap">
            <?php
            $current_user = Yii::$app->user->identity;
            $current_user_role = $current_user->userRole;

            if($order->use_fix_price == true) {
                $order->fix_price = $order->price;
            }
            if(in_array($current_user_role['alias'], ['admin', 'root'])) { ?>
                <input id="order-use_fix_price" name="Order[use_fix_price]" type="checkbox" <?= ($order->use_fix_price == true ? 'checked' : '') ?> style="vertical-align: bottom; margin-bottom: 3px;" class="label-horizontal"> <label class="label-horizontal" style="vertical-align: bottom;">фикс.</label>
                <div class="elem-horizontal" style="height: 48px;">
                    <label class="label-vertical">Фикс. цена</label>
                    <?php
                    echo $form->field($order, 'fix_price')
                        ->textInput([
                            'class' => 'input-text',
                        ])
                        ->widget(MaskMoney::class, [
                            'pluginOptions' => [
                                //'prefix' => "Р ",
                                'prefix' => "",
                                //'prefix' => '&#8381; ',
                                //'prefix' => "&#x20BD; ",
                                //'prefix' => htmlspecialchars('&#8381;') .' ',
                                'suffix' => '',
                                'affixesStay' => true,
                                'thousands' => ' ',
                                'decimal' => '',
                                'precision' => 0,
                                'allowZero' => false,
                                'allowNegative' => false,
                            ],
                            'options' => [
                                'class' => 'input-text',
                                'disabled' => !$order->use_fix_price,
                                //'maxlength' => 20
                            ]
                        ])
                        ->label(false);
                    ?>
                </div>
            <?php } elseif($order->use_fix_price == true) { ?>
                <input id="order-use_fix_price" name="Order[use_fix_price]" disabled="true" type="checkbox" <?= ($order->use_fix_price == true ? 'checked' : '') ?> style="vertical-align: bottom; margin-bottom: 3px;" class="label-horizontal"> <label class="label-horizontal" style="vertical-align: bottom;">фикс.</label>
                <div class="elem-horizontal" style="height: 48px;">
                    <label class="label-vertical">Фикс. цена</label>
                    <?php
                    echo $form->field($order, 'fix_price')
                        ->textInput([
                            'class' => 'input-text',
                        ])
                        ->widget(MaskMoney::class, [
                            'pluginOptions' => [
                                'prefix' => "Р ",
                                'suffix' => '',
                                'affixesStay' => true,
                                'thousands' => ' ',
                                'decimal' => '',
                                'precision' => 0,
                                'allowZero' => false,
                                'allowNegative' => false,
                            ],
                            'options' => [
                                'class' => 'input-text',
                                'disabled' => true,
                                //'maxlength' => 20
                            ]
                        ])
                        ->label(false);
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-11 mini-side-padding">
            <label class="label-vertical">Примечания к заказу</label>
            <?= $form->field($order, 'comment', ['errorOptions' => ['style' => 'display:none;']])
                ->textarea(['class' => 'input-text'])->label(false);
            ?>
        </div>
    </div>


    <div class="yellow-line" style="margin-top: 5px;">- Дополнительные телефоны заказа (если более 2 человек)</div>
    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 1</label>
            <?php
            if($mode == 'view') {

                echo '<input id="order-additional_phone_1_view" disabled="disabled" value="'.(!empty($client->additional_phone_1) ? Setting::changeShowingPhone($order->additional_phone_1, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="order-additional_phone_1" style="display: none;" name="Order[additional_phone_1]" value="'.$order->additional_phone_1.'" type="text">';

                /*
                if(!empty($order->additional_phone_1)) {
                    $order->additional_phone_1 = Setting::changeShowingPhone($order->additional_phone_1, 'show_short_clients_phones', 'x');
                }
                echo $form->field($order, 'additional_phone_1', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-***-***-****',
                        'options' => [
                            //'class' => 'call-phone',
                            'disabled' => true
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
                */

            }else {
                echo $form->field($order, 'additional_phone_1', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
            }
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 2</label>
            <?php
            if($mode == 'view') {

                echo '<input id="order-additional_phone_2_view" disabled="disabled" value="'.(!empty($client->additional_phone_2) ? Setting::changeShowingPhone($order->additional_phone_2, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="order-additional_phone_2" style="display: none;" name="Order[additional_phone_2]" value="'.$order->additional_phone_2.'" type="text">';

                /*
                if(!empty($order->additional_phone_2)) {
                    $order->additional_phone_2 = Setting::changeShowingPhone($order->additional_phone_2, 'show_short_clients_phones', 'x');
                }
                echo $form->field($order, 'additional_phone_2', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-***-***-****',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                            'disabled' => true
                        ],
                    ]);
                */

            }else {
                echo $form->field($order, 'additional_phone_2', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput(['class' => 'input-text'])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                    ]);
            }
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 3</label>
            <?php
            if($mode == 'view') {

                echo '<input id="order-additional_phone_3_view" disabled="disabled" value="'.(!empty($client->additional_phone_3) ? Setting::changeShowingPhone($order->additional_phone_3, 'show_short_clients_phones') : '').'" aria-required="true" type="text">';
                // скрытое поле, чтобы форма не потеряла телефон при сохранении
                echo '<input id="order-additional_phone_3" style="display: none;" name="Order[additional_phone_3]" value="'.$order->additional_phone_3.'" type="text">';

                /*
                if(!empty($order->additional_phone_3)) {
                    $order->additional_phone_3 = Setting::changeShowingPhone($order->additional_phone_3, 'show_short_clients_phones', 'x');
                }
                echo $form->field($order, 'additional_phone_3', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-***-***-****',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                            'disabled' => true
                        ],
                    ]);
                */

            }else {
                echo $form->field($order, 'additional_phone_3', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                    ]);
            }
            ?>
        </div>
    </div>


    <div class="yellow-line" style="margin-top: 5px; margin-bottom: 0;">- Откуда поедете?</div>
    <div class="row">
        <div class="col-sm-1 first-col" style="margin-top: 27px;"></div>
        <div class="col-sm-5 mini-side-padding nowrap">
            <a id="select-yandex-point-from" href="#" class="label-vertical">выбрать на карте</a>
            <?php
            // временная правка для последних созданных заказах до выгрузки кода с изменения в яндекс-точках
            if($order->yandex_point_from_id > 0 && empty($order->yandex_point_from_lat) && empty($order->yandex_point_from_long) && empty($order->yandex_point_from_name)) {
                $yandex_point_from = $order->yandexPointFrom;
                if($yandex_point_from == null) {
                    $value = '';
                    $initValueText = '';
                }else {
                    $value = $order->yandex_point_from_id.'_'.$yandex_point_from->lat.'_'.$yandex_point_from->long.'_'.$yandex_point_from->name;
                    $initValueText = $yandex_point_from->name;
                }
            }elseif($order->yandex_point_from_id == 0 && empty($order->yandex_point_from_lat) && empty($order->yandex_point_from_long) && empty($order->yandex_point_from_name)) {
                $value = '';
                $initValueText = '';
            }else {
                $value = (empty($order->yandex_point_from_id) ? 0 : $order->yandex_point_from_id).'_'.$order->yandex_point_from_lat.'_'.$order->yandex_point_from_long.'_'.$order->yandex_point_from_name;
                $initValueText = $order->yandex_point_from_name;
            }

            echo $form->field($order, 'yandex_point_from_id')->widget(SelectWidget::className(), [
                'initValueText' => $initValueText,
                'value' => $value,
                'options' => [
                    'name' => 'Order[yandex_point_from]',
                    'placeholder' => 'Введите название...',
                    'id' => 'yandex_point_from',
                ],
                'ajax' => [
                    'url' => '/yandex-point/ajax-yandex-points?is_from=1',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            direction_id: $("#direction").val()
                        };
                    }'),
                ],
                'add_new_value_url' => new JsExpression('function(params) {
                    // открытие карты...
                    var search = $(".sw-outer-block[attribute-name=\"Order[yandex_point_from]\"]").find(".sw-search").val();
                    openMapWithPointFrom(search);
                }'),
            ])->label(false);
            ?>
        </div>

        <div class="col-sm-1" style="width: 13.6%; ">&nbsp;</div>
        <div class="col-sm-3 first-col" style="width: 23%; margin-top: 22px;">
            <label class="label-horizontal" style="margin-top: 0;">Время приб. поезда<br />/ посадки самолета</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="width: 9.6%; margin-top: 22px;">
            <?php
            $options = [];
            $options['style'] = 'width: 100%; text-align: center;';
            if(empty($order->time_air_train_arrival)) {
                if(!($order->pointFrom != null && $order->pointFrom->alias == 'airport')) {
                    $options['disabled'] = true;
                }
            }

            echo $form->field($order, 'time_air_train_arrival')
            ->widget(\yii\widgets\MaskedInput::class, [
                'mask' => '99 : 99',
                'options' => $options,
                'clientOptions' => [
                    'placeholder' => '_'
                ]
            ])->label(false);
            ?>
        </div>
    </div>

    <div class="yellow-line" style="margin-top: 5px; margin-bottom: 0;">- Куда поедете?</div>
    <div class="row">

        <div class="col-sm-1 first-col" style="margin-top: 20px;">
        </div>
        <div class="col-sm-5 mini-side-padding nowrap" style="margin-top: 12px;">
            <?php
            // временная правка для последних созданных заказах до выгрузки кода с изменения в яндекс-точках
            if($order->yandex_point_to_id > 0 && empty($order->yandex_point_to_lat) && empty($order->yandex_point_to_long) && empty($order->yandex_point_to_name)) {
                $yandex_point_to = $order->yandexPointTo;
                if($yandex_point_to == null) {
                    $value = '';
                    $initValueText = '';
                }else {
                    $value = $order->yandex_point_to_id.'_'.$yandex_point_to->lat.'_'.$yandex_point_to->long.'_'.$yandex_point_to->name;
                    $initValueText = $yandex_point_to->name;
                }

            }elseif($order->yandex_point_to_id == 0 && empty($order->yandex_point_to_lat) && empty($order->yandex_point_to_long) && empty($order->yandex_point_to_name)) {
                $value = '';
                $initValueText = '';
            }else {
                $value = (empty($order->yandex_point_to_id) ? 0 : $order->yandex_point_to_id).'_'.$order->yandex_point_to_lat.'_'.$order->yandex_point_to_long.'_'.$order->yandex_point_to_name;
                $initValueText = $order->yandex_point_to_name;
            }
            echo $form->field($order, 'yandex_point_to_id')->widget(SelectWidget::className(), [
                'initValueText' => $initValueText,
                'value' => $value,
                'options' => [
                    'name' => 'Order[yandex_point_to]',
                    'placeholder' => 'Введите название...',
                    'id' => 'yandex_point_to',
                ],
                'ajax' => [
                    'url' => '/yandex-point/ajax-yandex-points?is_from=0',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            direction_id: $("#direction").val()
                        };
                    }')
                ],
            ])->label(false);
            ?>
        </div>

        <div class="col-sm-1" style="width: 13.6%; ">&nbsp;</div>
        <div class="col-sm-3 first-col" style="width: 23%; margin-top: 12px;">
            <label class="label-horizontal" style="margin-top: 0;">Время отпр. поезда<br />/ рег-ция авиарейса</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="width: 9.6%; margin-top: 12px;">
            <?php
            $options = [];
            $options['style'] = 'width: 100%; text-align: center;';
            if(empty($order->time_air_train_departure)) {
                if(!($order->yandexPointTo != null && $order->yandexPointTo->alias == 'airport')) {
                    $options['disabled'] = true;
                }
            }

            echo $form->field($order, 'time_air_train_departure')
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '99 : 99',
                    'options' => $options,
                    'clientOptions' => [
                        'placeholder' => '_'
                    ]
                ])->label(false);
            ?>
        </div>
    </div>

    <div class="yellow-line">
        - Место вам точно есть.
        <?= $form->field($order, 'radio_confirm_now', ['template' => "{label}\n{input}", 'options'=>['style' => "display: inline-block; width: 70%;"]])
            ->radioList(
                $order->radioConfirmNow,
                [
                    'item' => function ($index, $label, $name, $checked, $value) use($order) {

                        $checked = false;

                        return '<label style="font-weight: normal; margin: 0 0 0 10%;">' .
                            Html::radio($name, $checked, [
                                'value' => $value,
                                'style' => "vertical-align:bottom; margin:0; ",
                                'disabled' => $order->is_confirmed == 1,
                            ]) . ' <span id="radio_confirm_now_'.$value.'" text="'.$label.'">'.$label.'</span></label>';
                    },
                    'class' => $order->is_confirmed == 1 ? 'disabled' : '',
                ]
            )
            ->label(false);
        ?>
    </div>
    <div class="row">
        <div class="col-sm-1 first-col" style="width: 13.5%;">
            <?= Html::activeHiddenInput($order, 'time_confirm_auto') ?>
            <?php
            if($order->is_confirmed == 1 && $order->time_confirm > 0) {
                $order->time_confirm = date("H : i", $order->time_confirm);
            }else {
                $order->time_confirm = '';
            }
            echo $form->field($order, 'time_confirm', ['errorOptions' => ['style' => 'display:none;']])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '99 : 99',
                    'options' => [
                        'style' => 'width: 100%; padding-top: 3px; text-align: center;',
                        'disabled' => true,
                    ],
                    'clientOptions' => [
                        'placeholder' => '_'
                    ]
                ])
                ->label(false);
            ?>

            <?php if($order->is_confirmed == 1) { ?>
                <input id='confirm-button' type="button" value="Назначено" class="btn btn-success" style="font-size: 10px; padding: 3px 2px; width: 100%;" disabled="disabled" />
            <?php }else { ?>
                <input id='confirm-button' type="button" value="Установить" class="btn btn-default" style="font-size: 10px; padding: 3px 2px; width: 100%;" disabled="disabled" />
            <?php } ?>
            <?= Html::activeHiddenInput($order, 'confirm_click_time') ?>
            <?= Html::activeHiddenInput($order, 'confirm_clicker_id') ?>
        </div>

        <div class="col-sm-10">
            <div class="form-group field-order-radio_group_1">
                <input name="Order[radio_group_1]" value="" type="hidden">
                <div id="order-radio_group_1" class="disabled">
                    <?php

                    foreach($order->radioGroup1 as $value => $label) { ?>
                        <label style="font-weight: normal; margin: 0;">
                            <input name="Order[radio_group_1]" value="<?= $value ?>" disabled="disabled" style="vertical-align:bottom; margin:0;" type="radio">
                            <span id="radio_group_1_<?= $value ?>" text='<?= $order->clearRadioGroup1[$value] ?>'><?= $label ?></span>
                        </label>
                    <?php } ?>
                </div>
                <div class="help-block"></div>
            </div>

            <?= $form->field($order, 'radio_group_2')
                ->radioList(
                    $order->radioGroup2,
                    [
                        'item' => function ($index, $label, $name, $checked, $value) use($order)
                        {
                            $checked = false;

                            return '<label style="font-weight: normal; margin: 0;">' .
                                Html::radio($name, $checked, [
                                    'value' => $value,
                                    'style' => "vertical-align:bottom; margin:0;",
                                    'disabled' => true,
                                ]) . ' <span id="radio_group_2_'.$value.'" text="'.$label.'">'.$label.'</span></label>';
                        },
                        'class' => 'disabled'
                    ]
                )
                ->label(false);
            ?>
        </div>
    </div>
    <?php if(Yii::$app->setting->loyalty_switch == 'cash_back_on') { ?>
        <div class="yellow-line">К оплате <span id="resultPrice"><?= intval($order->price) ?></span> р. (полная стоимость - <span id="price"><?= (intval($order->price) + intval($order->used_cash_back)) ?></span> р., КБ - <span id="usedCashBack"><?= intval($order->used_cash_back) ?></span> р.)</div>
    <?php }else { // fifth_place_prize  ?>
        <div class="yellow-line">Стоимость проезда: <span id="price"><?= intval($order->price) ?></span> рублей (<span id="prizeTripCount"><?= $order->prizeTripCount ?></span> призовый поездок) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Оплачено: <?= $order->paid_summ ?> рублей</div>
    <?php } ?>
    <div class="row">
        <div class="col-sm-1  first-col" style="width: 13.5%;">&nbsp;</div>
        <div class="col-sm-10">
            <?php
            echo $form->field($order, 'radio_group_3')
                ->radioList(
                    $order->radioGroup3,
                    [
                        'item' => function ($index, $label, $name, $checked, $value) use($order) {

                            $checked = false;

                            return
                                '<label style="font-weight: normal; margin: 0; width: 100%;">' .
                                Html::radio($name, $checked, [
                                    'value' => $value,
                                    'style' => "vertical-align:bottom; margin:0; ",
                                    'disabled' => $order->is_confirmed != 1
                                ]) . ' <span id="radio_group_3_'.$value.'" text="'.$label.'">'.$label.'</span></label>';

                        },
                        'class' => ($order->is_confirmed != 1 ? 'disabled' : '')
                    ]
                )
                ->label(false);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2 first-col" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Записать', ['id' => 'writedown-button', 'class' => 'btn btn-success disabled', 'style' => 'padding: 3px 2px;  width: 100%;']) ?>
            </div>
        </div>
        <div class="col-sm-1" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Отменить', ['id' => 'cancel-button', 'class' => 'btn btn-default', 'style' => 'padding: 3px 4px;', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
    </div>

</div>


<?php
/*
if($mode == 'view') { ?>

    <div class="order-phones-block">
        <div class="order-phones-block-header">
            <button type="button" class="order-phones-block-close">×</button>
            <span class="order-phones-block-title">Изменение контактов</span>
        </div>
        <div class="order-phones-block-body">
            <div class="row">
                <label class="label-vertical">Моб. основной</label>
                <?php
                echo $form->field($client, 'mobile_phone_new')
                    ->textInput([])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [],
                        'clientOptions' => [
                            'placeholder' => '*',
                        ]
                    ]);
                ?>
            </div>

            <div class="row">
                <label class="label-vertical">ФИО</label>
                <?= $form->field($client, 'name_new', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text',
                        'placeholder' => 'Иванов Иван Иваныч',
                    ])->label(false) ?>
            </div>


            <div class="row">
                <label class="label-vertical">Домашний</label>
                <?php
                echo $form->field($client, 'home_phone_new')
                    ->textInput([
                        'class' => 'input-text',
                        //'placeholder' => '8-495-1234567',
                        //'disabled' => true
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            //'disabled' => ($order->direction_id > 0 ? false : true),
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
                ?>
            </div>

            <div class="row">
                <label class="label-vertical">Другой</label>
                <?php
                echo $form->field($client, 'alt_phone_new')
                    ->textInput([
                        //'class' => 'input-text',
                        //'disabled' => true,
                    ])
                    ->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            //'disabled' => ($order->direction_id > 0 ? false : true),
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);

                ?>
            </div>

            <div class="row">
                <label class="label-vertical">Доп. тел. 1</label>
                <?php
                echo $form->field($order, 'additional_phone_1_new', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                        'clientOptions' => [
                            'placeholder' => '*'
                        ]
                    ]);
                ?>
            </div>

            <div class="row">
                <label class="label-vertical">Доп. тел. 2</label>
                <?php
                echo $form->field($order, 'additional_phone_2_new', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput(['class' => 'input-text'])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                    ]);
                ?>
            </div>

            <div class="row">
                <label class="label-vertical">Доп. тел. 3</label>
                <?php
                echo $form->field($order, 'additional_phone_3_new', ['errorOptions' => ['style' => 'display:none;']])
                    ->textInput([
                        'class' => 'input-text'
                    ])->label(false)
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'mask' => '+7-999-999-9999',
                        'clientOptions' => [
                            'placeholder' => '*'
                        ],
                        'options' => [
                            //'class' => 'call-phone',
                        ],
                    ]);
                ?>
            </div>

        </div>

    </div>
<?php }
*/ ?>


<?php ActiveForm::end(); ?>