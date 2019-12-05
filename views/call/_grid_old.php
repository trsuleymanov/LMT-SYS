<?php

use app\components\Helper;
use app\models\Order;
use app\models\OrderPassenger;
use app\models\Setting;
use app\widgets\PopupFormWidget;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\widgets\EditableTextWidget;
use yii\web\JsExpression;


//\app\assets\AdminAsset::register($this);  // пока из админского ассета позаимстуем стили для таблицы

//$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
//$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['depends' => 'app\assets\AdminAsset']);


if($client != null) {

    $columns = [];

    // Рейс
    $columns[] = [
        'attribute' => 'trip_id',
        'label' => 'Рейс',
        'content' => function ($model) {

            $trip = $model->trip;

            if($trip != null) {
                // отправлен/не отправлен/отправка начата

                if(!empty($trip->date_sended)) {
                    $status = 'отправлен';
                }elseif(!empty($trip->date_start_sending)) {
                    $status = 'отправка начата';
                }else {
                    $status = 'не отправлен';
                }

                return Helper::getWeekDay($trip->date).', '.date('d.m.Y', $trip->date).'<br />'.$trip->direction->sh_name.' '.$trip->name.'('.$status.')';
            }else {
                return '';
            }
        }
    ];

    // Откуда+Куда
//    $columns[] = [
//        'attribute' => 'yandex_point_from_id',
//        'label' => "Откуда",
//        'content' => function($model) {
//
//            $yandexPoint = $model->yandexPointFrom;
//            if($yandexPoint != null && $yandexPoint->critical_point == 1) { // добавляем жирный шрифт
//                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
//                    return '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
//                }else { // просто отображается жирный шрифт обычного цвета
//                    return '<b>'.$model->yandex_point_from_name.'</b>';
//                }
//            }else { // отображаем обычный шрифт
//                return $model->yandex_point_from_name;
//            }
//        },
//        'filter' => false
//    ];
//
//
//    $columns[] = [
//        'attribute' => 'yandex_point_to_id',
//        'label' => "Куда",
//        'content' => function($model) {
//            if($model->yandex_point_to_id > 0) {
//                $yandex_point_to = $model->yandexPointTo;
//
//                if($yandex_point_to->critical_point == 1) { // добавляем жирный шрифт
//                    if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
//                        return '<b class="text-danger">'.$model->yandex_point_to_name.', '.$model->time_air_train_departure.'</b>';
//                    }else { // просто отображается жирный шрифт обычного цвета
//                        return '<b>'.$model->yandex_point_to_name.'</b>';
//                    }
//                }else { // отображаем обычный шрифт
//                    return $model->yandex_point_to_name;
//                }
//            }else {
//                return $model->yandex_point_to_name;
//            }
//        },
//        'filter' => false
//    ];

    // Откуда >>> Куда
    $columns[] = [
        'attribute' => 'yandex_point_from_id',
        'label' => "Откуда >>> Куда",
        'content' => function($model) {

            if($model->yandex_point_to_id > 0) {
                $yandex_point_to = $model->yandexPointTo;

                if($yandex_point_to->critical_point == 1) { // добавляем жирный шрифт
                    if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
                        $text_from = '<b class="text-danger">'.$model->yandex_point_to_name.', '.$model->time_air_train_departure.'</b>';
                    }else { // просто отображается жирный шрифт обычного цвета
                        $text_from = '<b>'.$model->yandex_point_to_name.'</b>';
                    }
                }else { // отображаем обычный шрифт
                    $text_from = $model->yandex_point_to_name;
                }
            }else {
                $text_from = $model->yandex_point_to_name;
            }

            $yandexPoint = $model->yandexPointFrom;
            if($yandexPoint != null && $yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                    $text_to = '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
                }else { // просто отображается жирный шрифт обычного цвета
                    $text_to = '<b>'.$model->yandex_point_from_name.'</b>';
                }
            }else { // отображаем обычный шрифт
                $text_to = $model->yandex_point_from_name;
            }

            return $text_from.' >>> '.$text_to;

        },
        'filter' => false
    ];

    $columns[] = [
        'attribute' => 'time_confirm',
        'label' => 'ВРПТ',
        'headerOptions' => [
            'style' => 'white-space: normal; width: 60px;',
            'title' => 'Время подтверждения'
        ],
        'contentOptions' => ['column-name' => 'time-confirm'],
        'content' => function ($model) use($canceled_order_status_id) {

            $trip = $model->trip;

            $options = [];
            if(!empty($model->time_sat) ||  ($trip != null && !empty($trip->date_sended)) || $model->status_id == $canceled_order_status_id) {
                $options['disabled'] = true;
            }

            return EditableTextWidget::widget([
                'name' => 'time_confirm',
                'value' => ($model->time_confirm > 0 ? date('H:i', $model->time_confirm) : ''),
                'defaultValue' => '<span class="text-danger">Укажите время</span>',
                'options' => $options,
                'mask' => '99:99',
                'onChange' => new JsExpression('function(id, etf_block, name, value) {
                    var order_id = "'.$model->id.'";
                    setTimeConfirm(order_id, value);
                }')
            ]);

        },
        'filter' => false
    ];

    $columns[] = [
        'attribute' => 'is_confirmed_without_sort',
        'label' => 'ПДТ',
        'headerOptions' => [
            'title' => 'Подтвердить'
        ],
        'content' => function ($model) use($canceled_order_status_id) {

            if($model->status_id == $canceled_order_status_id) {
                return '';

            }elseif(empty($model->is_confirmed)) {

                if(empty($model->time_confirm)) {
                    return Html::a(
                        '<i class="glyphicon glyphicon-ok"></i>',
                        '#',
                        [
                            'title' => 'Подтвердить (выберите Время подтверждения)',
                            'class' => "btn btn-success to-confirm",
                            'disabled' => true
                        ]
                    );
                }else {
                    return Html::a(
                        '<i class="glyphicon glyphicon-ok"></i>',
                        '#',
                        [
                            'title' => 'Подтвердить',
                            'class' => "btn btn-success to-confirm",
                        ]
                    );
                }
            }else {
                return '';
            }
        },
        'filter' => false
    ];

//    $columns[] = [
//        'attribute' => 'places_count',
//        'headerOptions' => [
//            'title' => 'Мест всего'
//        ],
//        //'contentOptions' => ['places-count' => $model->places_count, ],
//        'contentOptions' => function($model) {
//            return [
//                'class' => 'col-places-count',
//                'places-count' => $model->places_count
//            ];
//        },
//        'label' => 'М',
//        'content' => function($model) use($user_role_alias) {
//            if($model->is_not_places == 1) {
//                $value = '<b>–</b>';
//            }else {
//                $value = $model->places_count;
//            }
//
//            if(in_array($user_role_alias, ['root', 'admin'])){
//                return Html::a($value, Url::to('/admin/order?OrderSearch[id]='.$model->id), ['target' => '_blank']);
//            }else {
//                return $value;
//            }
//        },
//    ];

//    $columns[] = [
//        'attribute' => 'child_count',
//        'headerOptions' => [
//            'title' => 'Детский мест'
//        ],
//        'label' => 'Д',
//        'content' => function($model) {
//            return intval($model->child_count);
//        },
//    ];

//    $columns[] = [
//        'attribute' => 'student_count',
//        'headerOptions' => [
//            'title' => 'Студенческих мест'
//        ],
//        'label' => 'С',
//        'content' => function($model) {
//            return intval($model->student_count);
//        },
//    ];

//    $columns[] = [
//        'attribute' => 'bag_suitcase_oversized_count',
//        'label' => 'Багаж',
//        'headerOptions' => [
//            'title' => 'Количество чемоданов, сумок, негабаритов'
//        ],
//        'content' => function($model) {
//            return intval($model->suitcase_count).'Ч, '.intval($model->bag_count).'С, '.intval($model->oversized_count).'Н'; // чемоданы, сумки, негабариты
//        }
//    ];


    $columns[] = [
        'attribute' => 'places_count',
        'headerOptions' => [
            'title' => 'МДС/Б'
        ],
        //'contentOptions' => ['places-count' => $model->places_count, ],
        'contentOptions' => function($model) {
            return [
                'class' => 'col-places-count',
                'places-count' => $model->places_count
            ];
        },
        'label' => 'М',
        'content' => function($model) use($user_role_alias) {

            // 2М, 1Д, 1С <br> 0Ч, 0С, 0Н

            if($model->is_not_places == 1) {
                $places_count = 0;
            }else {
                $places_count = $model->places_count;
            }

            if(in_array($user_role_alias, ['root', 'admin'])){
                $places_count =  Html::a($places_count, Url::to('/admin/order?OrderSearch[id]='.$model->id), ['target' => '_blank']);
            }


            return
                $places_count.'М, '.intval($model->child_count).'Д, '.intval($model->student_count).'С<br />'
                .intval($model->suitcase_count).'Ч, '.intval($model->bag_count).'С, '.intval($model->oversized_count).'Н'; // чемоданы, сумки, негабариты
        },
    ];


    $columns[] = [
        'attribute' => 'comment',
        'label' => 'Примечания',
        'content' => function($model) use($canceled_order_status_id) {
            $str = '';

            if(!empty($model->comment)) {
                $str .= '<span>' . $model->comment . '</span><hr style="margin: 5px 0;" />';
            }
            $str .= 'Стоимость: <b>'.(intval($model->price) == 0 ? '0.00' : $model->price).'</b>';
            if($model->informer_office_id > 0) {
                $str .= ', <i>Ист. '.$model->informerOffice->name.'</i>';
            }
            if($model->relation_order_id > 0) {
                $relation_order = Order::findOne($model->relation_order_id);
                if($relation_order != null) {
                    $client = $relation_order->client;
                    $str .= '<br />Едет совместно с ' . $client->name . ' <span class="call-phone-button" phone="'.$client->mobile_phone.'">'.Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones').'</span>';
                }
            }

            if($model->status_id == $canceled_order_status_id && $model->cancellation_click_time > 0) {
                $str .=
                    '<br />Удалено в '.date('d.m.Y H:i', $model->cancellation_click_time)
                    .($model->cancellationClicker != null ? ' оператором '.$model->cancellationClicker->fullname : '')
                    .'<br />('.$model->cancellationReason->name.')';
            }


            // ВПЗ
            if(empty($model->first_writedown_click_time) && !empty( $model->time_vpz)) {
                $vpz = date('d.m.Y H:i', $model->first_writedown_click_time);
            }else {
                $vpz =
                    date('d.m.Y H:i', $model->first_writedown_click_time) .
                    ($model->firstWritedownClicker != null ?  ' '.$model->firstWritedownClicker->username : '');
            }


            return $str.'<br />ВПЗ: '.$vpz;
        },
        'filter' => false
    ];

//    $columns[] = [
//        //'attribute' => 'time_vpz',
//        'attribute' => 'first_writedown_click_time',
//        'label' => 'ВПЗ',
//        'headerOptions' => [
//            'title' => ' Время первичной записи'
//        ],
//        'filter' => DateRangePicker::widget([
//            'model' => $orderSearchModel,
//            //'attribute' => 'time_vpz',
//            'attribute' => 'first_writedown_click_time',
//            'convertFormat' => true,
//            'presetDropdown' => true,
//            'useWithAddon' => true,
//            'hideInput' => true,
//            'pluginOptions' => [
//                'locale' => [
//                    'format' => 'Y-m-d'
//                ],
//                'opens' => 'left',
//                'allowClear' => true,
//            ],
//        ]),
//        'filterOptions' => ['style' => 'white-space: nowrap;'],
//        'content' => function($model) {
//            //return date('d.m.Y H:i', $model->time_vpz);
//
//            // для страрых записей временный код - нужно будет удалить (создано 12.08.2018)
//            if(empty($model->first_writedown_click_time) && !empty( $model->time_vpz)) {
//                return date('d.m.Y H:i', $model->first_writedown_click_time);
//            }else {
//                return
//                    date('d.m.Y H:i', $model->first_writedown_click_time) .
//                    ($model->firstWritedownClicker != null ?  ' '.$model->firstWritedownClicker->username : '');
//            }
//        }
//    ];



    $columns[] = [

        'attribute' => 'fact_trip_transport_id',
        'label' => 'Машина',
        'headerOptions' => [
            'title' => 'Фактическое транспортное средство'
        ],
        'contentOptions' => [
            'class' => 'fact-trip-transport',
            'column-name' => 'fact-trip-transport',
        ],
        'content' => function($model) use($canceled_order_status_id)
        {
            $trip = $model->trip;
            $aTripTransports = ($trip != null ? $trip->getTripTransportList() : []);

            $trip_transport = $model->factTripTransport;

            $options = [];
            if(!empty($model->time_sat) || !empty($trip->date_sended) || $model->status_id == $canceled_order_status_id) {
                $options['disabled'] = true;
            }

            return PopupFormWidget::widget([
                'name' => 'fact_trip_transport_id',
                'value' => ($trip_transport != null ? $trip_transport->transport->name4 : ''),
                'defaultValue' => 'Выберите ТС',
                'options' => $options,
                'popupPosition' => 'left',
                'formContent' => Html::activeDropDownList($model, 'fact_trip_transport_id', $aTripTransports, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto; margin-right:10px;']),
                'onAccept' => new JsExpression('function(elem, form)
                {
                    var obj = $(this);
                    var fact_trip_transport_id = form.find("#order-fact_trip_transport_id").val();
                    $.ajax({
                        url: "/order/editable-order?id='.$model->id.'",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            fact_trip_transport_id: fact_trip_transport_id
                        },
                        success: function (data) {
                            //updateTripOrdersPage(); // обновляем всю страницу
                            //form.hide();

//                            var trip_id = $("#trip-orders-page").attr("trip-id");
//                            if(void 0 !== trip_id) {
//                                console.log("updateTripOrdersPage");
//                                updateTripOrdersPage(false, trip_id); // обновляем всю страницу
//                            }

                            //if($(".call-block").length > 0) {
                            //    console.log("updateCallClientOrders");
                            //    updateCallClientForm();
                            //}
                            updateCallModal();

                            form.hide();
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

                }'),
                'onCancel' => new JsExpression('function(elem, form) {
                    $.ajax({
                        url: "/order/editable-order?id='.$model->id.'",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            fact_trip_transport_id: ""
                        },
                        success: function (data) {
                            updateTripOrdersPage(); // обновляем всю страницу
                            form.hide();
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
                }'),
            ]);
        }
    ];


    $columns[] = [
        'attribute' => 'confirm_selected_transport',
        'headerOptions' => [
            'title' => 'Клиент знает машину'
        ],
        'contentOptions' => ['column-name' => 'confirm-selected-transport'],
        'label' => 'КЗМ?',
        'content' => function($model) use($canceled_order_status_id)
        {
            $trip = $model->trip;
            $aTripTransports = ($trip != null ? $trip->getTripTransportList() : []);


            if($model->confirm_selected_transport == 1) {
                //return '<span class="btn btn-sm" style="cursor: default;"><i class="glyphicon glyphicon-ok-sign btn-xs"></i></span>';
                return '<span class="glyphicon glyphicon-ok-sign btn-xs" style="margin: 4px 4px; font-size: 16px;"></span>';
            }else {
                if(!empty($model->time_confirm) && $model->is_confirmed == 1 && $model->fact_trip_transport_id > 0) {
                    return '<button class="btn btn-sm btn-warning confirm-selected-transport" type="button" title="Клиент знает машину ?"><i class="glyphicon glyphicon-question-sign"></i></button>';
                }else {
                    return '<button class="btn btn-sm btn-warning confirm-selected-transport" disabled="disabled" title="Выберите ВРПТ, подтвердите ВРПТ и выберите факт.т/с" type="button"><i class="glyphicon glyphicon-question-sign"></i></button>';
                }
            }
        }
    ];

    $columns[] = [
        'attribute' => 'time_sat_without_sort',// Посадить в машину
        'label' => "В т/с",
        'headerOptions' => [
            'style' => 'white-space: normal; width: 40px;',
            'title' => 'Посадить в транстпортное средство'
        ],
        'content' => function($model) use($aOrderStatuses)
        {
            $trip = $model->trip;

            if(isset($aOrderStatuses[$model->status_id]) && $aOrderStatuses[$model->status_id] == 'sent') {
                // если заказ уже отправлен, то ничего не показываем
                return '';
            }elseif(isset($aOrderStatuses[$model->status_id]) && $aOrderStatuses[$model->status_id] == 'canceled') {
                // если заказ находиться в статусе удален, то ничего не показываем
                return '';

            }elseif($model->confirm_selected_transport != 1) {
                return Html::a(
                    '<i class="glyphicon glyphicon-ok"></i>',
                    '#',
                    [
                        'title' => 'Посадить в машину (должно быть установлено КЗМ)',
                        'class' => "btn btn-success put-into-transport",
                        'disabled' => true
                    ]
                );

            }elseif(empty($trip->date_start_sending)) {
                return Html::a(
                    '<i class="glyphicon glyphicon-ok"></i>',
                    '#',
                    [
                        'title' => 'Посадить в машину (должна быть начата отправка рейса)',
                        'class' => "btn btn-success put-into-transport",
                        'disabled' => true
                    ]
                );

            }elseif(!empty($model->time_sat) && ($trip->use_mobile_app==1 && empty($model->confirmed_time_sat))) {
                //return '<button class="btn btn-sm btn-warning confirm-selected-transport" type="button" title="Клиент знает машину ?"><i class="glyphicon glyphicon-question-sign"></i></button>';
                return Html::a(
                    '<i class="glyphicon glyphicon-question-sign"></i>',
                    '#',
                    [
                        'title' => 'Подтвердить посадку машины',
                        'class' => "btn btn-success put-into-transport-confirm",
                    ]
                );

            }elseif(
                (!empty($model->time_sat) && ($trip->use_mobile_app==1 && !empty($model->confirmed_time_sat)))
                || (!empty($model->time_sat) && $trip->use_mobile_app==0)
            ) {
                // если "заказ" уже посажен в машину, то кнопка "высадить" - красная кнопка с крестом
                return Html::a(
                    '<i class="glyphicon glyphicon-remove"></i>',
                    '#',
                    [
                        'title' => 'Высадить из машины',
                        'class' => "btn btn-danger cancel-put-into-transport",
                    ]
                );
            }else { // confirm_selected_transport = 0
                // в остальных случах отображаем зеленую кнопку "посадить"
                return Html::a(
                    '<i class="glyphicon glyphicon-ok"></i>',
                    '#',
                    [
                        'title' => 'Посадить в машину',
                        'class' => "btn btn-success put-into-transport", // cancel-trip-transport
                    ]
                );
            }
        }
    ];

    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}',
        'header' => '',
        'buttons' => [
            'update' => function ($url, $model) {

                $trip = $model->trip;

                if(empty($model->time_sat) && empty($trip->date_sended)) {

//                    if($model->status_id == 2) { //canceled
//
//                        return Html::a(
//                            '<i class="glyphicon glyphicon-pencil"></i>',
//                            Url::to(['#']),
//                            [
//                                'title' => 'Воссоздать',
//                                'class' => "btn btn-warning create-order-copy",
//                                'order-id' => $model->id
//                            ]
//                        );
//
//                    }else {
                    return Html::a(
                        '<i class="glyphicon glyphicon-pencil"></i>',
                        Url::to(['#']),
                        [
                            'title' => 'Изменить',
                            'class' => "btn btn-warning edit-order",
                            'order-id' => $model->id
                        ]
                    );
//                    }
                }else {
                    return '';
                }
            },
        ],
    ];

    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{delete}',
        'header' => '',
        'buttons' => [
            'delete' => function ($url, $model) {

                $trip = $model->trip;

                if(empty($model->time_sat) && $model->status != null && $model->status->code != 'canceled' && empty($trip->date_sended)) {
                    return Html::a(
                        '<i class="glyphicon glyphicon-trash"></i>',
                        Url::to(['#']),
                        [
                            'title' => 'Отменить',
                            'class' => "btn btn-danger cancel-order",
                            'order-id' => $model->id
                        ]
                    );
                }else {
                    return '';
                }
            },
        ],
    ];

    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{edit_passengers}',
        'header' => '',
        'buttons' => [
            'edit_passengers' => function ($url, $model) {

                // нужно у текущего заказа получить список пассажиров
                $orders_passengers_count = OrderPassenger::find()->where(['order_id' => $model->id])->count();

                $trip = $model->trip;

                if(empty($trip->date_sended)) {
                    if ($model->places_count == 0) {
                        return '';
                    }

                    if ($orders_passengers_count == $model->places_count) {
                        $button_class = 'btn-success';
                    } else {
                        $button_class = 'btn-danger';
                    }

                    return Html::a(
                        '<i class="glyphicon glyphicon-copy"></i>',
                        Url::to(['#']),
                        [
                            'title' => 'Пассажиры',
                            'class' => "btn " . $button_class . " edit-passengers",
                            'order-id' => $model->id,
                        ]
                    );
                }else {
                    return '';
                }
            },
        ],
    ];


    echo GridView::widget([
        'id' => 'orders-grid',
        'layout' => '{items}',
        'dataProvider' => $orderDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) use($aOrderStatuses) {

            $background_color = '';
            if(isset($aOrderStatuses[$model->status_id])) {
                if($aOrderStatuses[$model->status_id] == 'canceled') {
                    $background_color = '#FFDBDB'; // красный
                }else {
                    if(!empty($model->time_confirm) && $model->is_confirmed == 1) {
                        $background_color = '#E6FFE6'; // зеленый
                    }else {
                        $background_color = '#FFF9B4'; // желтый
                    }
                }
            }else {
                $background_color = '#8b00ff';
            }

            return [
                'style' => 'background-color: '.$background_color.';',
                'order-id' => $model->id,
                'order-status' => $model->status == null ? 0 : $model->status->code
            ];
        },
        'columns' => $columns,
    ]);
    ?>
    <?php
} ?>

