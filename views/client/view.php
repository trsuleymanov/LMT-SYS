<?php

use app\models\InformerOffice;
use app\models\OrderCancellationReason;
use app\models\Setting;
use app\models\TripTransport;
use app\models\YandexPoint;
use yii\helpers\Html;
use app\widgets\EditableTextWidget;
use yii\web\JsExpression;
use yii\helpers\Url;
use app\models\Order;
use app\models\OrderStatus;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use app\models\Point;
use app\models\Trip;
use app\models\Direction;
use app\models\Transport;

$this->title = 'Лист информации о клиенте';
$this->params['breadcrumbs'][] = $this->title;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

$directions = Direction::find()->orderBy(['sh_name' => SORT_ASC])->all();

$aDirections = [];
if(count($directions) > 0) {
    foreach ($directions as $direction) {
        $aDirections[$direction->id] = $direction;
    }
}

// $start = microtime(true);

$client_orders = Order::find()->where(['client_id' => $client->id])->all();

$aTripsIds = [];
$aOrdersIds = [];
$aFactTripTransportsIds = [];
if(count($client_orders) > 0) {
    foreach ($client_orders as $order) {

        $aOrdersIds[] = $order->id;

        $aTripsIds[$order->trip_id] = $order->trip_id;

        if($order->fact_trip_transport_id > 0) {
            $aFactTripTransportsIds[$order->fact_trip_transport_id] = $order->fact_trip_transport_id;
        }
    }
}

$aTrips = [];
$trips = Trip::find()->where(['IN', 'id', $aTripsIds])->all();
if(count($trips) > 0) {
    foreach ($trips as $trip) {
        $aTrips[$trip->id] = $trip;
    }
}

$aFactTripTransports = [];
if(count($aFactTripTransportsIds) > 0) {
    $trip_transports = TripTransport::find()->where(['IN', 'id', $aFactTripTransportsIds])->all();
    if(count($trip_transports) > 0) {
        foreach ($trip_transports as $trip_transport) {
            $aFactTripTransports[$trip_transport->id] = $trip_transport;
        }
    }
}


$aYandexPoints = [];
$yandex_points = YandexPoint::find()->all();
if(count($yandex_points) > 0) {
    foreach ($yandex_points as $yandex_point) {
        $aYandexPoints[$yandex_point->id] = $yandex_point;
    }
}

$aInformerOffices = [];
$informer_offices = InformerOffice::find()->all();
if(count($informer_offices) > 0) {
    foreach ($informer_offices as $informer_office) {
        $aInformerOffices[$informer_office->id] = $informer_office;
    }
}

$aOrderCancellationReasons = [];
$order_cancellation_reasons = OrderCancellationReason::find()->all();
if(count($order_cancellation_reasons) > 0) {
    foreach ($order_cancellation_reasons as $order_cancellation_reason) {
        $aOrderCancellationReasons[$order_cancellation_reason->id] = $order_cancellation_reason;
    }
}

$aTransports = [];
$transports = Transport::find()->orderBy(['model' => SORT_ASC])->all();
if(count($transports) > 0) {
    foreach ($transports as $transport) {
        $aTransports[$transport->id] = $transport;
    }
}

$aLiteboxes = [];
$liteboxes = \app\models\LiteboxOperation::find()
    ->where(['IN', 'order_id', $aOrdersIds])
    ->andWhere(['sell_refund_at' => NULL])
    ->limit(20)
    ->all();
if(count($liteboxes) > 0) {
    foreach ($liteboxes as $litebox) {
        $aLiteboxes[$litebox->order_id][] = $litebox;
    }
}

// echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';

?>
<div id="client-view-page">

    <div class="row height-margin">
        <div class="col-md-2">ФИО:</div>
        <div class="col-md-3">
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
    </div>
    <div class="row height-margin">
        <div class="col-md-2">Мобильный телефон:</div>
        <div class="col-md-3">
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
                        //'receiver' => 'client_' . $client->id
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
        <div class="col-md-2">Домашний телефон:</div>
        <div class="col-md-3">
            <?php
            if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                echo EditableTextWidget::widget([
                    'name' => 'home_phone',
                    'value' => $client->home_phone,
                    'defaultValue' => '<span class="text-danger">Введите домашний телефон</span>',
                    'mask' => '+7-999-999-9999', // "8(999) 999-9999"
                    'options' => [
                        'class' => 'call-phone',
                        //'receiver' => 'client_' . $client->id
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
        <div class="col-md-2">Доп.телефон:</div>
        <div class="col-md-3">
            <?php
            if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

                echo EditableTextWidget::widget([
                    'name' => 'alt_phone',
                    'value' => $client->alt_phone,
                    'defaultValue' => '<span class="text-danger">Введите доп. телефон</span>',
                    'mask' => '+7-999-999-9999',
                    'options' => [
                        'class' => 'call-phone',
                        //'receiver' => 'client_' . $client->id
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
        <div class="col-md-2">Кэш-бэк:</div>
        <div class="col-md-3"><?= $client->cashback ?></div>
    </div>


    <br />
    <h3>Информация о заказах в текущем году:</h3>

    <?php if (!in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor'])) { ?>
        <div class="row">
            <div class="col-md-12">Всего заказано мест/заказов - <?= (intval($client->current_year_sended_places) + intval($client->current_year_canceled_places)) ?> м / <?= (intval($client->current_year_sended_orders) + intval($client->current_year_canceled_orders)) ?> з, из них:</div>
        </div>
        <div class="row">
            <div class="col-md-12"> - отправленных: <?= intval($client->current_year_sended_places) ?> м / <?= intval($client->current_year_sended_orders) ?> з</div>
        </div>
        <div class="row">
            <div class="col-md-12"> - отмененных: <?= intval($client->current_year_canceled_places) ?> м / <?= intval($client->current_year_canceled_orders)?> з</div>
        </div>
        <br />
    <?php } ?>


    <div class="row">
        <div class="col-md-12">Получено призовых: <?= intval($client->current_year_sended_prize_places) ?></div>
    </div>
    <div class="row">
        <div class="col-md-12">Штрафы: <?= intval($client->current_year_penalty) ?></div>
    </div>

    <br />
    <div class="row">
        <?php if(empty($client->current_year_sended_orders) && empty($client->current_year_canceled_orders)) { ?>
            <div class="col-md-12">Рейтинг отказов: NA</div>
        <?php }else { ?>
            <div class="col-md-12">Рейтинг отказов: <?= $client->getCurrentYear1hRejection() ?>% / <?= $client->getCurrentYear12hRejection() ?>%</div>
        <?php } ?>
    </div>

    <br />
    <div class="row">
        <div class="col-md-12">Мест/заказов с 1/1/<?= substr(date('Y'), 2) ?> по 13/1/<?= substr(date('Y'), 2) ?>: <?= intval($client->current_year_sended_113_places) ?> м / <?= intval($client->current_year_sended_113_orders) ?> з</div>
    </div>
    <div class="row">
        <div class="col-md-12">Мест/заказов на комм.рейсах: <?= intval($client->current_year_sended_commercial_places) ?> м / <?= intval($client->current_year_sended_commercial_orders) ?> з</div>
    </div>

    <br />
    <div class="row">
        <div class="col-md-12">Мест/заказов по фикс.цене: <?= intval($client->current_year_sended_fixprice_places) ?> м / <?= intval($client->current_year_sended_fixprice_orders) ?> з</div>
    </div>
    <div class="row">
        <div class="col-md-12">Мест/заказов по с безналичной оплатой: <?= intval($client->current_year_sended_informer_beznal_places) ?> м / <?= intval($client->current_year_sended_informer_beznal_orders) ?> з</div>
    </div>
    <div class="row">
        <div class="col-md-12">Посылок: <?= intval($client->current_year_sended_isnotplaces_orders) ?></div>
    </div>

    <br />
    <div class="box-body box-table">
        <?php
         echo GridView::widget([
            'dataProvider' => $dataProviderCurYear,
            'filterModel' => $searchModelCurYear,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout' => '{items}{pager}',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModelCurYear,
                        'attribute' => 'date',
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ],

                    ])
                ],
                [
                    'attribute' => 'time_sat',
                    'label' => 'Время посадки в т/с',
                    'headerOptions' => [
                        'title' => 'Время посадка в т/с'
                    ],
                    'content' => function ($model) {
                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModelCurYear,
                        'attribute' => 'time_sat',
                        'type' => DatePicker::TYPE_INPUT,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                [
                    'attribute' => 'direction_id',
                    'label' => 'Напр.',
                    'content' => function ($model) use($aDirections) {
                        //return $model->direction->sh_name;
                        return (isset($aDirections[$model->direction_id]) ? $aDirections[$model->direction_id]->sh_name : $model->direction_id);
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'direction_id',
                        ['' => 'Все'] + ArrayHelper::map($directions, 'id', 'sh_name'),
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'trip_id',
                    'content' => function($model) use($aTrips) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);

                        if($trip != null) {
                            return Html::a(
                                $trip->name,
                                Url::to(['/trip/trip-orders', 'trip_id' => $model->trip_id]),
                                [
                                    'title' => 'Состав рейса',
                                ]
                            );
                        }else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'trip_commercial',
                    'label' => 'ТР',
                    'content' => function($model) use($aTrips) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);
                        if($trip != null) {
                            if($trip->commercial == true) {
                                return 'КОММ';
                            }else {
                                return 'СТД';
                            }
                        }else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'yandex_point_from_name',
                    'label' => 'Точка откуда',
                    'content' => function($model) use($aYandexPoints) {
                        if($model->yandex_point_from_id > 0) {

                            //$yandexPoint = $model->yandexPointFrom;
                            $yandexPoint = (isset($aYandexPoints[$model->yandex_point_from_id]) ? $aYandexPoints[$model->yandex_point_from_id] : null);

                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_from_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_from_name;
                            }
                        }else {
                            return $model->yandex_point_from_name;
                        }

                    },

                ],
                [
                    'attribute' => 'yandex_point_to_name',
                    'label' => 'Точка куда',
                    'content' => function($model) use($aYandexPoints) {
                        if($model->yandex_point_to_id > 0) {

                            // $yandexPoint = $model->yandexPointTo;
                            $yandexPoint = (isset($aYandexPoints[$model->yandex_point_to_id]) ? $aYandexPoints[$model->yandex_point_to_id] : null);

                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_to_name.', '.$model->time_air_train_departure.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_to_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_to_name;
                            }
                        }else {
                            return $model->yandex_point_to_name;
                        }
                    },
                ],

                [
                    'attribute' => 'places_count',
                    'label' => 'М',
                    'content' => function($model) {
                        return $model->places_count > 0 ? $model->places_count : 0;
                    }
                ],
                [
                    'attribute' => 'bag_suitcase_oversized_count',
                    'label' => 'Багаж',
                    'headerOptions' => [
                        'title' => 'Количество чемоданов, сумок, негабаритов'
                    ],
                    'content' => function($model) {
                        return intval($model->suitcase_count).'Ч, '.intval($model->bag_count).'С, '.intval($model->oversized_count).'Н'; // чемоданы, сумки, негабариты
                    }
                ],
                [
                    'attribute' => 'status_id',
                    'content' => function ($model) use($aTrips, $aLiteboxes) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);

                        if($model->status_id > 0) {
                            if($model->status_id == 1) {
                                return 'Записан';
                            }elseif($model->status_id == 2) {
                                return 'Отменен';
                            }elseif($model->status_id == 3 && empty($trip->date_sended)) {
                                return 'Отправлен, не завершен';
                            }elseif($model->status_id == 3 && !empty($trip->date_sended)) {


                                $liteboxes = (isset($aLiteboxes[$model->order_id]) ? $aLiteboxes[$model->order_id] : []);

                                $str = 'Отправлен, завершен';

                                if(count($liteboxes) > 0) {

                                    $str .= '<br />';

                                    if ($model->payment_source == 'application') {
                                        $source = 'ILS_' . date('dmY_H:i', $model->paid_time);
                                    } elseif ($model->payment_source == 'client_site') {
                                        $source = 'Client_site_' . date('dmY_H:i', $model->paid_time);
                                    } elseif ($model->payment_source == 'crm') { //
                                        $source = 'CRM_' . date('dmY_H:i', $model->paid_time);
                                    } else {
                                        $source = 'неопределен_' . date('dmY_H:i', $model->paid_time);
                                    }

                                    foreach ($liteboxes as $litebox) {
                                        if(!empty($litebox->ecr_registration_number)) {
                                            $href = 'https://ofd.yandex.ru/vaucher/' . $litebox->ecr_registration_number . '/' . $litebox->fiscal_document_number . '/' . $litebox->fiscal_document_attribute;
                                            $str .= '<span><a href="' . $href . '">' . $source . '</a></span><br />';
                                        }
                                    }
                                }

                                return $str;
                            }
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'status_id',
                        //['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        [0 => 'Все', 1 => 'Записан', 2 => 'Отменен', 3 => 'Отправлен, не завершен', 4 => 'Отправлен, завершен'],
                        ['class' => "form-control"]
                    )
                ],

//                'prize_trip_count',
                [
                    'attribute' => 'price',
                    'label' => 'Цена',
                    'content' => function($model) {

                        $str = Yii::$app->formatter->asCurrency($model->price);

                        $str .= ($model->prize_trip_count > 0 ? '<br />призовых: '.$model->prize_trip_count.'' : '');

                        if($model->accrual_cash_back > 0) {
                            $str .= '<br />начислено кэш-бэка: ' . $model->accrual_cash_back . '<br />';
                        }
                        if($model->penalty_cash_back > 0) {
                            $str .= '<br />оштрафовано кэш-бэка: ' . $model->penalty_cash_back . '<br />';
                        }
                        if($model->used_cash_back > 0) {
                            $str .= '<br />использовано кэш-бэка для оплаты заказа: ' . $model->used_cash_back . '<br />';
                        }
                        if($model->prize_trip_count == 1 && !empty($model->comment)) {
                            $str .= '<br />'.$model->comment. '<br />';
                        }

                        return $str;
                    },
                ],
                [
                    'attribute' => 'fact_transport_id',
                    'label' => 'Факт. т/с',
                    'content' => function($model) use($aFactTripTransports, $aOrderCancellationReasons, $aTransports) {

//                        $factTripTransport = $model->factTripTransport;

                        $factTripTransport = ($model->fact_trip_transport_id > 0 && isset($aFactTripTransports[$model->fact_trip_transport_id]) ? $aFactTripTransports[$model->fact_trip_transport_id] : null);

                        $text = '';
                        $transport = null;
                        if($factTripTransport != null && isset($aTransports[$factTripTransport->transport_id])) {
                            $transport = $aTransports[$factTripTransport->transport_id];
                        }
                        if($transport != null) {
                            $text = $transport->name2;
                        }


                        // return $text.(!empty($model->cancellation_reason_id) ? $model->cancellationReason->name.', '.($model->has_penalty == 1 ? 'оштрафован, '.$model->penalty_comment : 'не оштрафован') : '');
                        return $text.
                            (!empty($model->cancellation_reason_id) && isset($aOrderCancellationReasons[$model->cancellation_reason_id]) ? $aOrderCancellationReasons[$model->cancellation_reason_id]->name.', '.($model->has_penalty == 1 ? 'оштрафован, '.$model->penalty_comment : 'не оштрафован') : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'fact_transport_id',
                        ['' => 'Все'] + ArrayHelper::map($transports, 'id', 'name2'),
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'informer_office_id',
                    'content' => function($model) use($aInformerOffices) {

                        //return ($model->informer_office_id > 0 && $model->informerOffice != null ? $model->informerOffice->name : '');
                        return ($model->informer_office_id > 0 && isset($aInformerOffices[$model->informer_office_id]) ? $aInformerOffices[$model->informer_office_id]->name : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'informer_office_id',
                        ['' => 'Все'] + ArrayHelper::map($informer_offices, 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
            ],
        ]);
         ?>
    </div>






    <hr />
    <br />
    <h3>Информация о заказах в прошлом:</h3>

    <?php if (!in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor'])) { ?>
        <div class="row">
            <div class="col-md-12">Всего заказано мест/заказов - <?= (intval($client->past_years_sended_places) + intval($client->past_years_canceled_places)) ?> м / <?= (intval($client->past_years_sended_orders) + intval($client->past_years_canceled_orders)) ?> з, из них:</div>
        </div>
        <div class="row">
            <div class="col-md-12"> - отправленных: <?= intval($client->past_years_sended_places) ?> м / <?= intval($client->past_years_sended_orders) ?> з</div>
        </div>
        <div class="row">
            <div class="col-md-12"> - отмененных: <?= intval($client->past_years_canceled_places) ?> м / <?= intval($client->past_years_canceled_orders)?> з</div>
        </div>
        <br />
    <?php } ?>


    <div class="row">
        <div class="col-md-12">Получено призовых: <?= intval($client->past_years_sended_prize_places) ?></div>
    </div>
    <div class="row">
        <div class="col-md-12">Штрафы: <?= intval($client->past_years_penalty) ?></div>
    </div>

    <br />
    <div class="row">
        <?php
        // надежность по заказам в прошлом
//        $past_years_orders_reliability = 0;
//        $total_past_orders = $client->past_years_sended_orders + $client->past_years_canceled_orders;
//        if($total_past_orders > 0) {
//            $past_years_orders_reliability = round(100*$client->past_years_sended_orders / $total_past_orders, 0);
//        }

//        // надежность по местам
//        $past_years_places_reliability = 0;
//        $total_past_places = $client->past_years_sended_places + $client->past_years_canceled_places;
//        if($total_past_places > 0) {
//            $past_years_places_reliability = round(100*$client->past_years_sended_places / $total_past_places, 0);
//        }
        ?>
        <?php /*
        <div class="col-md-12">Надежность: <?= $client->getPastYearsSendedOrders() ?>% / <?= $client->getPastYearsPlacesReliability() ?>%</div>
        */ ?>
        <?php if(empty($client->past_years_sended_orders) && empty($client->past_years_canceled_orders)) { ?>
            <div class="col-md-12">Рейтинг отказов: NA</div>
        <?php }else { ?>
            <div class="col-md-12">Рейтинг отказов: <?= $client->getPastYears1hRejection() ?>% / <?= $client->getPastYears12hRejection() ?>%</div>
        <?php } ?>
    </div>

    <br />
    <div class="row">
        <div class="col-md-12">Мест/заказов по фикс.цене: <?= intval($client->past_years_sended_fixprice_places) ?> м / <?= intval($client->past_years_sended_fixprice_orders) ?> з</div>
    </div>
    <div class="row">
        <div class="col-md-12">Мест/заказов по с безналичной оплатой: <?= intval($client->past_years_sended_informer_beznal_places) ?> м / <?= intval($client->past_years_sended_informer_beznal_orders) ?> з</div>
    </div>
    <div class="row">
        <div class="col-md-12">Посылок: <?= intval($client->past_years_sended_isnotplaces_orders) ?></div>
    </div>


    <br />
    <div class="box-body box-table">
        <?php

         echo GridView::widget([
            'dataProvider' => $dataProviderPastYears,
            'filterModel' => $searchModelPastYears,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout' => '{items}{pager}',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModelPastYears,
                        'attribute' => 'date',
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ],

                    ])
                ],
                [
                    'attribute' => 'time_sat',
                    'label' => 'Время посадки в т/с',
                    'headerOptions' => [
                        'title' => 'Время посадка в т/с'
                    ],
                    'content' => function ($model) {
                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModelPastYears,
                        'attribute' => 'time_sat',
                        'type' => DatePicker::TYPE_INPUT,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                [
                    'attribute' => 'direction_id',
                    'label' => 'Напр.',
                    'content' => function ($model) use($aDirections) {
                        // return $model->direction->sh_name;
                        return (isset($aDirections[$model->direction_id]) ? $aDirections[$model->direction_id]->sh_name : $model->direction_id);
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelPastYears,
                        'direction_id',
                        ['' => 'Все'] + ArrayHelper::map($directions, 'id', 'sh_name'),
                        ['class' => "form-control"]
                    )
                ],


                // перегружает систему на клиенте http://tobus-yii2.ru/client/view?id=29552
                [
                    'attribute' => 'trip_id',
                    'content' => function($model) use($aTrips) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);

                        if($trip != null) {
                            return Html::a(
                                $trip->name,
                                Url::to(['/trip/trip-orders', 'trip_id' => $model->trip_id]),
                                [
                                    'title' => 'Состав рейса',
                                ]
                            );
                        }else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'trip_commercial',
                    'label' => 'ТР',
                    'content' => function($model) use($aTrips) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);

                        if($trip != null) {
                            if($trip->commercial == true) {
                                return 'КОММ';
                            }else {
                                return 'СТД';
                            }
                        }else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'yandex_point_from_name',
                    'label' => 'Точка откуда',
                    'content' => function($model) use($aYandexPoints) {
                        if($model->yandex_point_from_id > 0) {

                            // $yandexPoint = $model->yandexPointFrom;
                            $yandexPoint = (isset($aYandexPoints[$model->yandex_point_from_id]) ? $aYandexPoints[$model->yandex_point_from_id] : null);

                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_from_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_from_name;
                            }
                        }else {
                            return $model->yandex_point_from_name;
                        }

                    },

                ],
                [
                    'attribute' => 'yandex_point_to_name',
                    'label' => 'Точка куда',
                    'content' => function($model) use($aYandexPoints) {
                        if($model->yandex_point_to_id > 0) {

                            // $yandexPoint = $model->yandexPointTo;
                            $yandexPoint = (isset($aYandexPoints[$model->yandex_point_to_id]) ? $aYandexPoints[$model->yandex_point_to_id] : null);

                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_to_name.', '.$model->time_air_train_departure.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_to_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_to_name;
                            }
                        }else {
                            return $model->yandex_point_to_name;
                        }
                    },
                ],

                [
                    'attribute' => 'places_count',
                    'label' => 'М',
                    'content' => function($model) {
                        return $model->places_count > 0 ? $model->places_count : 0;
                    }
                ],
                [
                    'attribute' => 'bag_suitcase_oversized_count',
                    'label' => 'Багаж',
                    'headerOptions' => [
                        'title' => 'Количество чемоданов, сумок, негабаритов'
                    ],
                    'content' => function($model) {
                        return intval($model->suitcase_count).'Ч, '.intval($model->bag_count).'С, '.intval($model->oversized_count).'Н'; // чемоданы, сумки, негабариты
                    }
                ],
                [
                    'attribute' => 'status_id',
                    'content' => function ($model) use($aTrips) {

                        $trip = (isset($aTrips[$model->trip_id]) ? $aTrips[$model->trip_id] : null);

                        if($model->status_id > 0) {
                            if($model->status_id == 1) {
                                return 'Записан';
                            }elseif($model->status_id == 2) {
                                return 'Отменен';
                            }elseif($model->status_id == 3 && empty($trip->date_sended)) {
                                return 'Отправлен, не завершен';
                            }elseif($model->status_id == 3 && !empty($trip->date_sended)) {
                                return 'Отправлен, завершен';
                            }
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'status_id',
                        //['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        [0 => 'Все', 1 => 'Записан', 2 => 'Отменен', 3 => 'Отправлен, не завершен', 4 => 'Отправлен, завершен'],
                        ['class' => "form-control"]
                    )
                ],


                [
                    'attribute' => 'price',
                    'label' => 'Цена',
                    'content' => function($model) {

                        $str = Yii::$app->formatter->asCurrency($model->price);

                        $str .= ($model->prize_trip_count > 0 ? '<br />призовых: '.$model->prize_trip_count.'' : '');

                        if($model->accrual_cash_back > 0) {
                            $str .= '<br />начислено кэш-бэка: ' . $model->accrual_cash_back . '<br />';
                        }
                        if($model->penalty_cash_back > 0) {
                            $str .= '<br />оштрафовано кэш-бэка: ' . $model->penalty_cash_back . '<br />';
                        }
                        if($model->used_cash_back > 0) {
                            $str .= '<br />использовано кэш-бэка для оплаты заказа: ' . $model->used_cash_back . '<br />';
                        }

                        return $str;
                    },
                ],
                [
                    'attribute' => 'fact_transport_id',
                    'label' => 'Факт. т/с',
                    'content' => function($model) use($aFactTripTransports, $aOrderCancellationReasons, $aTransports) {

                        //$factTripTransport = $model->factTripTransport;
                        $factTripTransport = ($model->fact_trip_transport_id > 0 && isset($aFactTripTransports[$model->fact_trip_transport_id]) ? $aFactTripTransports[$model->fact_trip_transport_id] : null);

                        $text = '';
                        $transport = null;
                        if($factTripTransport != null && isset($aTransports[$factTripTransport->transport_id])) {
                            $transport = $aTransports[$factTripTransport->transport_id];
                        }
                        if($transport != null) {
                            $text = $transport->name2;
                        }

                        // return $text .(!empty($model->cancellation_reason_id) ? $model->cancellationReason->name.', '.($model->has_penalty == 1 ? 'оштрафован, '.$model->penalty_comment : 'не оштрафован') : '');
                        return $text.
                            (!empty($model->cancellation_reason_id) && isset($aOrderCancellationReasons[$model->cancellation_reason_id]) ? $aOrderCancellationReasons[$model->cancellation_reason_id]->name.', '.($model->has_penalty == 1 ? 'оштрафован, '.$model->penalty_comment : 'не оштрафован') : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelPastYears,
                        'fact_transport_id',
                        ['' => 'Все'] + ArrayHelper::map($transports, 'id', 'name2'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'informer_office_id',
                    'content' => function($model) use($aInformerOffices) {
                        // return ($model->informer_office_id > 0 && $model->informerOffice != null ? $model->informerOffice->name : '');
                        return ($model->informer_office_id > 0 && isset($aInformerOffices[$model->informer_office_id]) ? $aInformerOffices[$model->informer_office_id]->name : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModelCurYear,
                        'informer_office_id',
                        ['' => 'Все'] + ArrayHelper::map($informer_offices, 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
            ],
        ]);

        ?>
    </div>



<?php /* ?>
    <hr />
    <br />
    <h3>Информация о заказах:</h3>

    <?php if (!in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor'])) { ?>
        <div class="row height-margin">
            <div class="col-md-5">Отмененных мест / Отправлено мест:</div>
            <div class="col-md-3"><?= ($client->current_year_canceled_places + $client->past_years_canceled_places) ?> / <?= ($client->current_year_sended_places + $client->past_years_sended_places) ?></div>
        </div>
    <?php } ?>

    <div class="row height-margin">
        <div class="col-md-5">Поездок (мест в заказах) по фикс.цене:</div>
        <div class="col-md-3"><?= $client->sended_fixprice_orders_places_count ?></div>
    </div>
    <div class="row height-margin">
        <div class="col-md-5">Посылок (заказов):</div>
        <div class="col-md-3"><?= $client->sended_is_not_places_order_count  ?></div>
    </div>
    <div class="row height-margin">
        <div class="col-md-5">Поездок (мест в заказах) с информаторскими с безналичной оплатой:</div>
        <div class="col-md-3"><?= $client->sended_informer_beznal_orders_places_count ?></div>
    </div>

    <div class="row height-margin">
        <div class="col-md-5">Призовых поездок:</div>
        <div class="col-md-3"><?= $client->sended_prize_trip_count ?></div>
    </div>

    <?php if (!in_array(Yii::$app->session->get('role_alias'), ['manager', 'editor'])) { ?>
        <div class="row height-margin">
            <div class="col-md-5">Штрафов:</div>
            <div class="col-md-3"><?= $client->penalty ?></div>
        </div>
    <?php } ?>

    <br /><br />

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout' => '{items}{pager}',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date',
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ],

                    ])
                ],
                [
                    'attribute' => 'time_sat',
                    'label' => 'Время посадки в т/с',
                    'headerOptions' => [
                        'title' => 'Время посадка в т/с'
                    ],
                    'content' => function ($model) {
                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_sat',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                [
                    'attribute' => 'direction_id',
                    'label' => 'Направление',
                    'content' => function ($model) {
                        return $model->direction->sh_name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'direction_id',
                        ['' => 'Все'] + ArrayHelper::map(Direction::find()->orderBy(['sh_name' => SORT_ASC])->all(), 'id', 'sh_name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'trip_id',
                    'content' => function($model) {
                        if($model->trip != null) {
                            return Html::a(
                                $model->trip->name,
                                Url::to(['/trip/trip-orders', 'trip_id' => $model->trip_id]),
                                [
                                    'title' => 'Состав рейса',
                                ]
                            );
                        }else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'yandex_point_from_name',
                    'label' => 'Точка откуда',
                    'content' => function($model) {
                        if($model->yandex_point_from_id > 0) {
                            $yandexPoint = $model->yandexPointFrom;
                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_from_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_from_name;
                            }
                        }else {
                            return $model->yandex_point_from_name;
                        }

                    },

                ],
                [
                    'attribute' => 'yandex_point_to_name',
                    'label' => 'Точка куда',
                    'content' => function($model) {
                        if($model->yandex_point_to_id > 0) {
                            $yandexPoint = $model->yandexPointTo;

                            if($yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                                if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
                                    return '<b class="text-danger">'.$model->yandex_point_to_name.', '.$model->time_air_train_departure.'</b>';
                                }else { // просто отображается жирный шрифт обычного цвета
                                    return '<b>'.$model->yandex_point_to_name.'</b>';
                                }
                            }else { // отображаем обычный шрифт
                                return $model->yandex_point_to_name;
                            }
                        }else {
                            return $model->yandex_point_to_name;
                        }
                    },
                ],

                [
                    'attribute' => 'places_count',
                    'label' => 'М',
                    'content' => function($model) {
                        return $model->places_count > 0 ? $model->places_count : 0;
                    }
                ],
                [
                    'attribute' => 'bag_suitcase_oversized_count',
                    'label' => 'Багаж',
                    'headerOptions' => [
                        'title' => 'Количество чемоданов, сумок, негабаритов'
                    ],
                    'content' => function($model) {
                        return intval($model->suitcase_count).'Ч, '.intval($model->bag_count).'С, '.intval($model->oversized_count).'Н'; // чемоданы, сумки, негабариты
                    }
                ],
                [
                    'attribute' => 'status_id',
                    'content' => function ($model) {
                        if($model->status_id > 0) {
                            return $model->status->name;
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status_id',
                        ['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],

//                'prize_trip_count',
                [
                    'attribute' => 'price',
                    'label' => 'Цена',
                    'content' => function($model) {
                        return Yii::$app->formatter->asCurrency($model->price).($model->prize_trip_count > 0 ? '<br />призовых: '.$model->prize_trip_count.'' : '');
                    },
                ],
                [
                    'attribute' => 'fact_transport_id',
                    'label' => 'Факт. т/с',
                    'content' => function($model) {
                        $factTripTransport = $model->factTripTransport;
                        if($factTripTransport != null) {
                            $text = $factTripTransport->transport->name2;
                        }else {
                            $text = '';
                        }

                        return $text.(!empty($model->cancellation_reason_id) ? $model->cancellationReason->name.', '.($model->has_penalty == 1 ? 'оштрафован, '.$model->penalty_comment : 'не оштрафован') : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'fact_transport_id',
                        ['' => 'Все'] + ArrayHelper::map(Transport::find()->orderBy(['model' => SORT_ASC])->all(), 'id', 'name2'),
                        ['class' => "form-control"]
                    )
                ],
            ],
        ]); ?>
    </div>
<? */ ?>

    <div id="bottom-navigation">
        <div class="container">
            <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Вернуться на главную', '/', ['class' => 'btn btn-warning pull-left']) ?>
        </div>
    </div>
</div>
