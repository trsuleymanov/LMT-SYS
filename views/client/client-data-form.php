<?php
use app\models\Order;
use app\models\OrderStatus;
use app\models\Setting;
use app\widgets\EditableTextWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;

?>

<div class="row height-margin">
    <div class="col-md-4" align="right">ФИО:</div>
    <div class="col-md-6">
        <?= EditableTextWidget::widget([
            'name' => 'name',
            'value' => $client->name,
            'defaultValue' => '<span class="text-danger">Введите имя</span>',
            'onChange' => new JsExpression('function(id, etf_block, name, value) {
                $.ajax({
                    url: "/client/editable-client?id='.$client->id.'",
                    type: "post",
                    data: {
                        hasEditable: 1,
                        name: value
                    },
                    success: function (data) {
                        if(data.message != "") {
                            alert(data.message);
                        }else {
                            etf_block.hide();
                            if(data.output == "") {
                                $("#" + id).html("<span class=\"text-danger\">Введите имя</span>").show();
                            }else {
                                $("#" + id).text(data.output).show();
                            }
                        }
                    },
                    error: function (data, textStatus, jqXHR) {
                        if (textStatus == "error") {
                            if (void 0 !== data.responseJSON) {
                                if (data.responseJSON.message.length > 0) {
                                    alert(data.responseJSON.message);
                                }
                            } else {
                                if (data.responseText.length > 0) {
                                    alert(data.responseText);
                                }
                            }
                        }
                    }
                });
            }')
        ]);
        ?>
    </div>
    <div class="col-md-2" text-align="right">
        <?= Html::a(
            '<i class="glyphicon glyphicon-align-justify"></i>',
            Url::to(['/client/view', 'id' => $client->id]),
            [
                'title' => 'Редактировать клиента',
                'class' => "btn btn-default",
                'style' => 'position: absolute; background: #EEEEEE;',
                'target' => '_blank'
            ]
        ); ?>
    </div>
</div>
<div class="row height-margin">
    <div class="col-md-4" align="right">Мобильный телефон:</div>
    <div class="col-md-6">
        <?php
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

            echo EditableTextWidget::widget([
                'name' => 'mobile_phone',
                'value' => $client->mobile_phone,
                'defaultValue' => '<span class="text-danger">Введите мобильный телефон</span>',
                'mask' => '+7-999-999-9999',
                'options' => [
                    'disabled' => in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor']),
                    'class' => 'call-phone',
                    //'receiver' => 'client_'.$client->id
                ],
                'onChange' => new JsExpression('function(id, etf_block, name, value) {
                    $.ajax({
                        url: "/client/editable-client?id=' . $client->id . '",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            mobile_phone: value
                        },
                        success: function (data) {
                            if(data.message != "") {
                                alert(data.message);
                            }else {
                                etf_block.hide();
                                if(data.output == "") {
                                    $("#" + id).html("<span class=\"text-danger\">Введите мобильный телефон</span>").show();
                                }else {
                                    $("#" + id).text(data.output).show();
                                }
                            }
                        },
                        error: function (data, textStatus, jqXHR) {
                            if (textStatus == "error") {
                                if (void 0 !== data.responseJSON) {
                                    if (data.responseJSON.message.length > 0) {
                                        alert(data.responseJSON.message);
                                    }
                                } else {
                                    if (data.responseText.length > 0) {
                                        alert(data.responseText);
                                    }
                                }
                            }
                        }
                    });
                }')
            ]);
    }else {
            if(!empty($client->mobile_phone)) {
                echo '<span class="call-phone-button" phone="'.$client->mobile_phone.'">'.Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones');
            }
    }

        ?>
    </div>
</div>
<div class="row height-margin">
    <div class="col-md-4" align="right">Домашний телефон:</div>
    <div class="col-md-6">
        <?php
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            echo EditableTextWidget::widget([
                'name' => 'home_phone',
                'value' => $client->home_phone,
                'defaultValue' => '<span class="text-danger">Введите домашний телефон</span>',
                'mask' => '+7-999-999-9999', // "8(999) 999-9999"
                'options' => [
                    'class' => 'call-phone',
                    //'receiver' => 'client_'.$client->id
                ],
                'onChange' => new JsExpression('function(id, etf_block, name, value) {
                    $.ajax({
                        url: "/client/editable-client?id=' . $client->id . '",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            home_phone: value
                        },
                        success: function (data) {
                            if(data.message != "") {
                                alert(data.message);
                            }else {
                                etf_block.hide();
                                if(data.output == "") {
                                    $("#" + id).html("<span class=\"text-danger\">Введите домашний телефон</span>").show();
                                }else {
                                    $("#" + id).text(data.output).show();
                                }
                            }
                        },
                        error: function (data, textStatus, jqXHR) {
                            if (textStatus == "error") {
                                if (void 0 !== data.responseJSON) {
                                    if (data.responseJSON.message.length > 0) {
                                        alert(data.responseJSON.message);
                                    }
                                } else {
                                    if (data.responseText.length > 0) {
                                        alert(data.responseText);
                                    }
                                }
                            }
                        }
                    });
                }')
            ]);
        }else {
            if(!empty($client->home_phone)) {
                echo '<span class="call-phone-button" phone="'.$client->home_phone.'">'.Setting::changeShowingPhone($client->home_phone, 'show_short_clients_phones').'<span>';
            }
        }
        ?>
    </div>
</div>
<div class="row height-margin">
    <div class="col-md-4" align="right">Доп.телефон:</div>
    <div class="col-md-6">
        <?php
        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

            echo EditableTextWidget::widget([
                'name' => 'alt_phone',
                'value' => $client->alt_phone,
                'defaultValue' => '<span class="text-danger">Введите доп. телефон</span>',
                'mask' => '+7-999-999-9999',
                'options' => [
                    'class' => 'call-phone',
                    //'receiver' => 'client_'.$client->id
                ],
                'onChange' => new JsExpression('function(id, etf_block, name, value) {
                    $.ajax({
                        url: "/client/editable-client?id=' . $client->id . '",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            alt_phone: value
                        },
                        success: function (data) {
                            if(data.message != "") {
                                alert(data.message);
                            }else {
                                etf_block.hide();
                                if(data.output == "") {
                                    $("#" + id).html("<span class=\"text-danger\">Введите доп. телефон</span>").show();
                                }else {
                                    $("#" + id).text(data.output).show();
                                }
                            }
                        },
                        error: function (data, textStatus, jqXHR) {
                            if (textStatus == "error") {
                                if (void 0 !== data.responseJSON) {
                                    if (data.responseJSON.message.length > 0) {
                                        alert(data.responseJSON.message);
                                    }
                                } else {
                                    if (data.responseText.length > 0) {
                                        alert(data.responseText);
                                    }
                                }
                            }
                        }
                    });
                }')
            ]);

        }else {
            if(!empty($client->alt_phone)) {
                echo '<span class="call-phone-button" phone="'.$client->alt_phone.'">'.Setting::changeShowingPhone($client->alt_phone, 'show_short_clients_phones').'<span>';
            }
        }
        ?>
    </div>
</div>
<div class="row height-margin">
    <div class="col-md-4" align="right">Мест в отправленных заказах:</div>
    <div class="col-md-6"><?= ($client->current_year_sended_places + $client->past_years_sended_places)  ?></div>
</div>
<?php if (!in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor'])) { ?>
    <div class="row height-margin">
        <div class="col-md-4" align="right">Мест в отмененных заказах:</div>
        <div class="col-md-6"><?= ($client->current_year_canceled_places + $client->past_years_canceled_places)  ?></div>
    </div>
<?php } ?>