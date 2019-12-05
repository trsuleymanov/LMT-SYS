<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
<?php if($mode == 'view') { ?>

    <?php
    $form = ActiveForm::begin([
        //'id' => 'order-client-form',
        //'action' => ($order->id > 0 ? Url::to(['ajax-update-order', 'id' => $order->id]) : Url::to(['ajax-create-order'])),
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
//        'options' => [
//            'order-id' => $order->id,
//            'order-temp-identifier' => $order->temp_identifier,
//            'order-passengers-count' => $order_passengers_count,
//            //'mode' => $mode
//        ],
    ]);
    ?>


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
                <?php
                $client->name_new = $client->name;
                echo $form->field($client, 'name_new', ['errorOptions' => ['style' => 'display:none;']])
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

            <br />
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


            <div class="row">
                <div class="col-sm-6" style="padding-left: 0;" >
                    <div class="form-group">
                        <?= Html::button('Изменить', ['id' => 'order-phones-copy-button', 'class' => 'btn btn-success', 'style' => 'padding: 3px 5px; width: 100%;']) ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <?= Html::button('Отменить', ['class' => 'btn btn-default order-phones-block-close', 'style' => 'padding: 3px 5px;']) ?>
                    </div>
                </div>
            </div>


        </div>

    </div>

    <?php ActiveForm::end(); ?>

<?php } ?>

