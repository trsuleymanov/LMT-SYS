<?php
/*
 * Заказы на рейсе + данные рейса и выбранной машины
 */

use app\models\Setting;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use app\models\OrderStatus;
use yii\helpers\ArrayHelper;
use app\models\YandexPoint;
use app\models\Order;
use yii\helpers\Url;
use app\widgets\PopupFormWidget;
use yii\web\JsExpression;
use app\widgets\EditableTextWidget;
use yii\widgets\MaskedInput;
use yii\widgets\Breadcrumbs;
use app\models\OrderPassenger;

$this->title = 'Состав рейса '.$trip->direction->sh_name.' '.date("d.m.Y", $trip->date).' '.$trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')';
if(!empty($trip->date_sended)) {
    $this->title .= ' - рейс отправлен';
}elseif(!empty($trip->date_start_sending)) {
    $this->title .= ' - Отправка рейса начата';
}
if($trip->use_mobile_app === 0) {
    $this->title .= ' (стандартный режим)';
}elseif($trip->use_mobile_app === 1) {
    $this->title .= ' (интерактивный режим)';
}

$this->params['breadcrumbs'][] = $this->title;


$aYandexPointList = ArrayHelper::map(YandexPoint::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
$aOrderStatuses = ArrayHelper::map(OrderStatus::find()->all(), 'id', 'code');

$canceled_order_status_id = array_flip($aOrderStatuses)['canceled'];

$trip_orders = Order::find()
    ->where(['trip_id' => $trip->id])
    ->andWhere(['!=', 'status_id', $canceled_order_status_id])
    ->all();
// echo "count_orders=".count($trip_orders)."<br />";

$trip_price = 0;
$confirmed_places = 0;
$trip_orders_places_count = 0;
$aTripTransportsTotalPrices = [];
foreach($trip_orders as $trip_order) {
    $trip_price += $trip_order->price;
    $trip_orders_places_count += $trip_order->places_count;
    if($trip_order->is_confirmed == 1) {
        $confirmed_places += $trip_order->places_count;
    }
    if(isset($aTripTransportsTotalPrices[$trip_order->fact_trip_transport_id])) {
        $aTripTransportsTotalPrices[$trip_order->fact_trip_transport_id] += $trip_order->price;
    }else {
        $aTripTransportsTotalPrices[$trip_order->fact_trip_transport_id] = $trip_order->price;
    }
}

$deleted_count = 0;
$deleted_trip_orders = Order::find()->where(['trip_id' => $trip->id, 'status_id' => $canceled_order_status_id])->all();
foreach($deleted_trip_orders as $deleted_order) {
    $deleted_count += $deleted_order->places_count;
}

$aTripTransports = [];
$count_transports_places = 0;
$trip_transports = $trip->tripTransports;
if(count($trip_transports) > 0) {
    foreach($trip_transports as $trip_transport) {
        $transport = $trip_transport->transport;
        $count_transports_places += $transport->places_count;
//        if(empty($trip_transport->date_sended)) {
//            $aTripTransports[$trip_transport->id] = $transport->name4;
//        }
        $aTripTransports[$trip_transport->id] = $transport->name4;
    }
}

// пассажиры отсортированные по заказам
$aOrdersPassengers = [];
$orders_passengers = OrderPassenger::find()->where(['order_id' => ArrayHelper::map($trip_orders, 'id', 'id')])->all();
foreach($orders_passengers as $order_passenger) {
    $aOrdersPassengers[$order_passenger->order_id][] = $order_passenger;
}

$user = Yii::$app->user->identity;
$user_role_alias = $user->userRole->alias;

//0 - кол-во подтвержденных мест - зеленым шрифтом
//4 - количество записанных мест - черным шрифтом
//2 - количество удаленных  мест - красным шрифтом
//16 -общее количество мест в машине - черным шрифтом

$show_reis = false;
if(empty($trip->date_sended)) {
    $show_reis = true;
}elseif(time() < $trip->date_sended + 6000) {
    $show_reis = true;
}elseif(!empty($trip->date_sended) && $user_role_alias == 'root') {
    $show_reis = true;
}
?>

<?php if($show_reis == true) { ?>
    <div id="reis-panel" reis-name="<?= $trip->direction->sh_name.' '.date("d.m.Y", $trip->date).' '.$trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')' ?>">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
<?php } ?>


<?php
//if(empty($trip->date_sended)) {
if(empty($trip->date_issued_by_operator) && empty($trip->date_sended)) {
?>
    <div id="reis-panel-left">
        <?= Html::a('Записать сюда', '#', ['id' => 'add-order', 'class' => 'btn btn-success btn-sm', 'date' => $trip->date, 'trip-id' => $trip->id]); ?>

        <?= Html::a('Карта', '#', ['id' => 'trip-yandex-map', 'class' => 'btn btn-sm btn-primary']); ?>

        <span id="trip-price">ОСР - <?= $trip_price ?></span>

        <?php if(Yii::$app->session->get('role_alias') != 'manager' && $trip->is_reserv == false) { ?>
            <?= Html::a('Добавить ТС', '#', ['id' => 'add-trip-transport-car', 'class' => 'btn btn-sm btn-primary pull-right_', 'trip-id' => $trip->id]); ?>
        <?php } ?>

        <span class="places-count pull-right_">
            <span title="Подтвержденных мест" class="text-green"><?= $confirmed_places ?></span>/<span title="Всего мест"><?= $trip_orders_places_count ?></span>/<span title="Удаленных мест" class="text-red"><?= $deleted_count ?></span>/<span title="Мест в т/с"><?= $count_transports_places ?></span>
        </span>
    </div>
    <div id="reis-panel-right">
        <?= Html::a('Запись на сегодня', '#', ['id' => 'new-order-today', 'class' => 'btn btn-sm btn-default',]); ?>
        <?= Html::a('Запись на завтра', '#', ['id' => 'new-order-tomorrow', 'class' => 'btn btn-sm btn-default',]); ?>
        <?= Html::a('Записать на другой день', '#', ['id' => 'new-order-another-day', 'class' => 'btn btn-sm btn-default']); ?>
    </div>

<?php } ?>

<?php if(!empty($trip->date_sended) && ($user_role_alias == 'root' || time() < $trip->date_sended + 6000)) { ?>
    <div id="reis-panel-left">
        <?php if(time() < $trip->date_sended + 6000) {
            // echo Html::a('Записать сюда', '#', ['id' => 'add-order', 'class' => 'btn btn-success btn-sm', 'date' => $trip->date, 'trip-id' => $trip->id, 'style' => 'margin-right: 20px;']);
        }
        if($user_role_alias == 'root') {
            echo Html::a('Отменить отпраку рейса и всех отправленных машин', '#', ['id' => 'cancel-trip-sended', 'class' => 'btn btn-danger', 'trip-id' => $trip->id]);
        } ?>
    </div>
<?php } ?>

<?php if($show_reis == true) { ?>
    </div>
<?php } ?>

<br /><br /><br /><br /><br />
<?php

if($trip->is_reserv == false) {

    $columns = [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'transport_car_reg',
            'label' => 'Номер',
            'content' => function ($model) {
                $transport = $model->transport;
                return ($transport == null ? '' : '<a href="#" class="trip_transport" trip_transport_id="' . $model->id . '">' . $transport->car_reg . '</a>');
            }
        ],
        [
            'attribute' => 'transport_model',
            'label' => 'Модель',
            'content' => function ($model) {
                $transport = $model->transport;
                return ($transport == null ? '' : $transport->model . ' ' . $transport->color);
            }
        ],
    ];

    if (empty($trip->date_sended) && !empty($trip->date_issued_by_operator)) {
        $columns[] = [
            'attribute' => 'add_order',
            'label' => '',
            'content' => function ($model) use ($trip) {

                //if(empty($trip->date_sended) && !empty($trip->date_issued_by_operator)) {
                if ($model->used_places_count < $model->total_places_count) {
                    return '<a class="btn btn-success btn-sm add-order" href="#" date="' . $trip->date . '" trip-id="' . $trip->id . '" trip-transport-id="' . $model->id . '">Записать сюда</a>';
                } else {
                    return '';
                }
                //}
            }
        ];
    }


    if ($trip->use_mobile_app == 1 && !empty($trip->date_start_sending)) {
        $columns[] = [
            'attribute' => 'access_key',
            'label' => 'Идентификатор доступа',
            'content' => function ($model) {
                if (!empty($model->access_key)) {
                    return substr($model->access_key, 0, 4) . '-' . substr($model->access_key, 4, 4) . '-' . substr($model->access_key, 8);
                } else {
                    return '';
                }
            }
        ];
    }


    $columns[] = [
        'attribute' => 'transport_places_count',
        'headerOptions' => ['style' => 'white-space: normal; width: 190px;'],
        'label' => 'Запланировано / Знает номер / Кол-во мест',
        'content' => function ($model) {
            $transport = $model->transport;
            if ($transport == null) {
                return '0/0/0';
            } else {
                return $model->factPlacesCount . '/' . $model->factKZMPlacesCount . '/' . $transport->places_count;
            }
        }
    ];

    $columns[] = [
        'attribute' => 'driver_id',
        'label' => 'Водитель',
        'content' => function ($model) {
            $driver = $model->driver;
            return ($driver == null ? '' : $driver->fio);
        }
    ];
    $columns[] = [
        'attribute' => 'transport_orders_places_count',
        'headerOptions' => ['style' => 'white-space: normal; width: 190px;'],
        //'label' => $trip->use_mobile_app == 1 ? 'В машине сидит / Отправлено на посадку' : 'В машине сидит',
        'label' => 'В машине сидит / Отправлено на посадку',
        'content' => function ($model) use ($trip) {

            // $set_to_transport_count = 0;
            $site_in_transport_count = 0;
            $fact_orders = $model->factOrdersWithoutCanceled;
            foreach ($fact_orders as $fact_order) {
                // $set_to_transport_count += ($fact_order->time_sat > 0 ? $fact_order->places_count : 0);
                $site_in_transport_count += ($fact_order->confirmed_time_sat > 0 ? $fact_order->places_count : 0);
            }

            // return $site_in_transport_count . ' / ' . $set_to_transport_count;
            return $site_in_transport_count . ' / ' . intval($model->used_places_count);
        }
    ];

    $columns[] = [
        'attribute' => 'osr',
        'label' => 'ОСР',
        'content' => function ($model) use ($aTripTransportsTotalPrices) {
            return (isset($aTripTransportsTotalPrices[$model->id]) ? $aTripTransportsTotalPrices[$model->id] : 0);
        }
    ];

    $columns[] = [
        'attribute' => 'status_id',
        'label' => 'Статус',
        'content' => function ($model) {
            return $model->statusName;
        }
    ];

    if (Yii::$app->session->get('role_alias') != 'manager') {
        $columns[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'header' => 'Отправить',
            'buttons' => [
                'update' => function ($url, $model) use ($trip) {
                    if ($model->status_id == 1) {
                        return '';
                    } elseif ($model->confirmed != 1) {
                        return Html::a(
                            '<i class="glyphicon glyphicon-ok"></i>',
                            '#',
                            [
                                'title' => 'Отправить (необходимо подтвердить т/с)',
                                'class' => "btn btn-success",
                                'trip-transport-id' => $model->id,
                                'disabled' => true
                            ]
                        );
                    } elseif (empty($trip->date_start_sending)) {
                        return Html::a(
                            '<i class="glyphicon glyphicon-ok"></i>',
                            '#',
                            [
                                'title' => 'Отправить (необходимо начать отправку рейса)',
                                'class' => "btn btn-success",
                                'trip-transport-id' => $model->id,
                                'disabled' => true
                            ]
                        );
                    } else {
                        return Html::a(
                            '<i class="glyphicon glyphicon-ok"></i>',
                            '#',
                            [
                                'title' => 'Отправить',
                                'class' => "btn btn-success question-send-trip-transport",
                                'trip-transport-id' => $model->id
                            ]
                        );
                    }
                },
            ],
        ];

        $columns[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'header' => 'Отменить',
            'buttons' => [
                'delete' => function ($url, $model) {
                    if ($model->status_id == 1) {
                        return '';
                    } else {
                        return Html::a(
                            '<i class="glyphicon glyphicon-remove"></i>',
                            '#',
                            [
                                'title' => 'Отменить',
                                'class' => "btn btn-danger cancel-trip-transport",
                                'trip-transport-id' => $model->id
                            ]
                        );
                    }
                },
            ],
        ];
    }


    echo GridView::widget([
        'id' => 'trip-transports-grid',
        'dataProvider' => $transportDataProvider,
        //'filterModel' => $transportSearchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {

            if (!empty($model->date_sended)) { // отправлен - синий
                $background_color = '#92B9F4';// 256DFF
            } else {
                if ($model->confirmed == 1) { // подтвержден - зеленый
                    $background_color = '#E6FFE6';
                } else {     // не подтвержден - желтый
                    $background_color = '#FFF9B4';
                }
            }

            return [
                'style' => 'background-color: ' . $background_color . ';',
                'class' => $model->confirmed == 1 ? 'confirmed-transport' : '',
                'transport-name' => trim($model->transport->name4)
            ];
        },
        'layout' => '{items}',
        'options' => [
            'class' => 'grid-view table-responsive',
            'style' => 'margin-top: 70px;'
        ],
        'tableOptions' => [
            'class' => 'table table-condensed table-bordered table-hover'
        ],
        'columns' => $columns
    ]);
?>
<br />
<?php } ?>

<div id="orders-buttons-block" style="display: none;">
    <div class="row">
        <div class="col-sm-6">
            <label>Привязать к выбранным заказам</label>
            <?= Html::dropDownList('orders_fact_trip_transport_id', key($aTripTransports), $aTripTransports, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto; margin:0 10px;']); ?>
            <button id="orders_fact_trip_transport_accept" class="btn btn-sm btn-success pfw-accept" type="button"><i class="glyphicon glyphicon-ok"></i></button>
            <button id="orders_fact_trip_transport_cancel" class="btn btn-sm btn-danger pfw-cancel" type="button"><i class="glyphicon glyphicon-remove"></i></button>
        </div>
        <div class="col-sm-6">
            <label>Назначить выбранным заказам ВРПТ</label>
            <?= MaskedInput::widget([
                'name' => 'orders_time_confirm',
                //'value' => $trip->name,
                'mask' => '99:99',
                'options' => [
                    'class' => 'form-control',
                    'style' => 'display: inline-block; width: 51px; margin:0 10px; padding: 6px;'
                ],
            ]);
            ?>
            <button id="orders_time_confirm_accept" class="btn btn-sm btn-success pfw-accept" type="button"><i class="glyphicon glyphicon-ok"></i></button>
            <button id="orders_time_confirm_cancel" class="btn btn-sm btn-danger pfw-cancel" type="button"><i class="glyphicon glyphicon-remove"></i></button>
        </div>
    </div>
</div>

<br />
<div class="legend">
    <div class="l-title">Статусы заказов</div>
    <div class="mark expects"></div>
    <span class="l-text"> - Не подтвержден</span>
    <div class="mark sended"></div>
    <span class="l-text"> - Подтвержден</span>
    <div class="mark cancel"></div>
    <span class="l-text"> - Отменен</span>
</div>

<?php




$columns = [];

if(empty($trip->date_sended)) {
    //$columns[] = ['class' => 'yii\grid\CheckboxColumn'];

    $columns[] = [
        //'class' => 'yii\grid\CheckboxColumn',
        'header' => '<input class="select-on-check-all" name="selection_all" value="1" type="checkbox">',
        'content' => function($model, $key, $index, $column) use($canceled_order_status_id) {
            if(empty($model->time_sat) && $model->status_id != $canceled_order_status_id) {
                return '<input name="selection[]" value="'.$key.'" type="checkbox">';
            }else {
                return '';
            }
        }
    ];
}


$columns = ArrayHelper::merge($columns, [
//    [
//        'attribute' => 'id',
//        'label' => 'id заказа',
//    ],
    [
        'attribute' => 'yandex_point_from_id',
        'label' => "Откуда",
        'content' => function($model) {


            $yandexPoint = $model->yandexPointFrom;
            if($yandexPoint != null && $yandexPoint->critical_point == 1) { // добавляем жирный шрифт
                if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                    return '<b class="text-danger">'.$model->yandex_point_from_name.', '.$model->time_air_train_arrival.'</b>';
                }else { // просто отображается жирный шрифт обычного цвета
                    return '<b>'.$model->yandex_point_from_name.'</b>';
                }
            }else { // отображаем обычный шрифт
                return $model->yandex_point_from_name;
            }

        },
        'filter' => Html::activeDropDownList(
            $orderSearchModel,
            'yandex_point_from_id',
            ['' => 'Все'] + $aYandexPointList,
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'time_confirm_sort',
        'label' => 'ПРС',
        'headerOptions' => [
            'style' => 'white-space: normal; width: 60px;',
            'title' => 'Поле ручной сортировки'
        ],
        'contentOptions' => ['column-name' => 'time-confirm-sort'],
        'content' => function($model) use($trip, $canceled_order_status_id) {

            if(!empty($model->time_sat) || !empty($trip->date_sended) || $model->status_id == $canceled_order_status_id) {
                return $model->time_confirm_sort;
            }elseif(empty($model->time_confirm_sort)) {
                return '';
            }else {
                return
                    '<button class="time-confirm-sort-minus">-</button>'
                    . '<span class="time-confirm-sort">' . $model->time_confirm_sort . '</span>'
                    . '<button class="time-confirm-sort-plus">+</button>';
            }
        }
    ],
    [
        'attribute' => 'time_confirm',
        'label' => 'ВРПТ',
        'headerOptions' => [
            'style' => 'white-space: normal; width: 60px;',
            'title' => 'Время подтверждения'
        ],
        'contentOptions' => ['column-name' => 'time-confirm'],
        'content' => function ($model) use($trip, $canceled_order_status_id) {
            $options = [];
            if(!empty($model->time_sat) || !empty($trip->date_sended) || $model->status_id == $canceled_order_status_id) {
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

        }
    ],
    [
        'attribute' => 'time_confirm',
        'label' => 'А',
        'content' => function ($model) {
            return $model->time_confirm_auto > 0 ? '<a href="" class="time-confirm-auto">'.date('H:i', $model->time_confirm_auto).'</a>' : '';
        }
    ],

    [
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
        }
    ],
    [
        'attribute' => 'yandex_point_to_id',
        'label' => "Куда",
        'content' => function($model) {
            if($model->yandex_point_to_id > 0) {
                $yandex_point_to = $model->yandexPointTo;

                if($yandex_point_to->critical_point == 1) { // добавляем жирный шрифт
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
        'filter' => Html::activeDropDownList(
            $orderSearchModel,
            'yandex_point_to_id',
            ['' => 'Все'] + $aYandexPointList,
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'places_count',
        'headerOptions' => [
            'title' => 'Мест всего'
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
            if($model->is_not_places == 1) {
                $value = '<b>–</b>';
            }else {
                $value = $model->places_count;
            }

            if(in_array($user_role_alias, ['root', 'admin'])){
                return Html::a($value, Url::to('/admin/order?OrderSearch[id]='.$model->id), ['target' => '_blank']);
            }else {
                return $value;
            }
        },
    ],
    [
        'attribute' => 'child_count',
        'headerOptions' => [
            'title' => 'Детский мест'
        ],
        'label' => 'Д',
        'content' => function($model) {
            return intval($model->child_count);
        },
    ],
    [
        'attribute' => 'student_count',
        'headerOptions' => [
            'title' => 'Студенческих мест'
        ],
        'label' => 'С',
        'content' => function($model) {
            return intval($model->student_count);
        },
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
        'attribute' => 'comment',
        'label' => 'Примечания',
        'content' => function($model) use($canceled_order_status_id, $trip) {
            $str = '';

            if(!empty($model->comment)) {
                $str .= '<span>' . $model->comment . '</span><hr style="margin: 5px 0;" />';
            }
            //if($model->cash_received_time > 0) {
                //$str .= '<span>Деньги получены водителем</span><br />';

                $liteboxes = \app\models\LiteboxOperation::find()
                    ->where(['order_id' => $model->id])
                    ->andWhere(['sell_refund_at' => NULL])
                    ->limit(20)
                    ->all();

                if(count($liteboxes) > 0)
                {
                    //$href = 'https://ofd.yandex.ru/vaucher/'.$model->litebox_ecr_registration_number.'/'.$model->litebox_fiscal_document_number.'/'.$model->litebox_fiscal_document_attribute;
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
            //}
            $str .= 'Стоимость/Оплачено: <b>'.(intval($model->price) == 0 ? '0.00' : $model->price).' / '.($model->paid_summ == 0 ? '0.00' : $model->paid_summ).'</b>';
            if($model->informer_office_id > 0) {
                $str .= ', <i>Ист. '.$model->informerOffice->name.'</i>';
            }
            if($model->relation_order_id > 0) {
                $relation_order = Order::findOne($model->relation_order_id);
                if($relation_order != null) {
                    $client = $relation_order->client;
                    if($client != null) {
                        $str .= '<br />Едет совместно с ' . $client->name . ' <span class="call-phone-button" phone="'.$client->mobile_phone.'">' . (!empty($client->mobile_phone) ? Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones') : '') . '</span>';
                    }
                }
            }

            if($model->status_id == $canceled_order_status_id && $model->cancellation_click_time > 0) {
                $str .=
                    '<br />Удалено в '.date('d.m.Y H:i', $model->cancellation_click_time)
                    .($model->cancellationClicker != null ? ' оператором '.$model->cancellationClicker->fullname : '')
                    .'<br />('.($model->cancellationReason != null ? $model->cancellationReason->name : '').')';
            }

            // если заказ не оплачен, то оператор может отметить оплату с выдачей чека
            // и если после отправки т/с (заказа) прошло 30 минут
            if($model->status_id == 3 && $model->is_paid == false && $model->status_setting_time + 18 < time()) {
                $str .= ' <button class="but-pay-and-make-check" title="Заказ становиться оплаченным и выдается чек" style="margin: 0; padding: 0; line-height: 10px;"><i class="glyphicon glyphicon-piggy-bank"></i></button>';
            }

            return $str;
        }
    ],
    [
        //'attribute' => 'time_vpz',
        'attribute' => 'first_writedown_click_time',
        'label' => 'ВПЗ',
        'headerOptions' => [
            'title' => ' Время первичной записи'
        ],
        'filter' => DateRangePicker::widget([
            'model' => $orderSearchModel,
            //'attribute' => 'time_vpz',
            'attribute' => 'first_writedown_click_time',
            'convertFormat' => true,
            'presetDropdown' => true,
            'useWithAddon' => true,
            'hideInput' => true,
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d'
                ],
                'opens' => 'left',
                'allowClear' => true,
            ],
        ]),
        'filterOptions' => ['style' => 'white-space: nowrap;'],
        'content' => function($model) {
            //return date('d.m.Y H:i', $model->time_vpz);

            // для страрых записей временный код - нужно будет удалить (создано 12.08.2018)
//            if(empty($model->first_writedown_click_time) && !empty( $model->time_vpz)) {
//                return date('d.m.Y H:i', $model->first_writedown_click_time);
//            }else {
//                return
//                    date('d.m.Y H:i', $model->first_writedown_click_time) .
//                    ($model->firstWritedownClicker != null ?  ' '.$model->firstWritedownClicker->username : '');
//            }

            if($model->first_writedown_click_time > 0) {
                return
                    date('d.m.Y H:i', $model->first_writedown_click_time) .
                    ($model->firstWritedownClicker != null ? ' ' . $model->firstWritedownClicker->username : '');
            }else {
                return '';
            }
        }
    ],
    [
        'attribute' => 'client_name_phone',
        'label' => 'Клиент',
        'headerOptions' => [
            'style' => 'max-width: 150px;'
        ],
        'content' => function ($model) use($user_role_alias) {
            $client = $model->client;

            if(in_array($user_role_alias, ['root', 'admin'])){
                $str = '<a href="/client/view?id='.$client->id.'" target="_blank">'.$client->name.'</a>';
            }else {
                $str = $client->name;
            }

            $str =
                $str
                . '<br /><span class="text-nowrap">М. <span class="call-phone-button" phone="'.$client->mobile_phone.'">'.Setting::changeShowingPhone($client->mobile_phone, 'show_short_clients_phones').'</span>'
                . (!empty($client->home_phone) ? '<br /> Д. <span class="call-phone-button" phone="'.$client->home_phone.'">'.Setting::changeShowingPhone($client->home_phone, 'show_short_clients_phones') : '').'</span></span>'
                . (!empty($client->alt_phone) ? '<br/><span class="text-nowrap">Другой <span class="call-phone-button" phone="'.$client->alt_phone.'">'.Setting::changeShowingPhone($client->alt_phone, 'show_short_clients_phones').'</span>' : '')
                . (!empty($model->additional_phone_1) ? '<br/><span class="text-nowrap">Другой <span class="call-phone-button" phone="'.$model->additional_phone_1.'">'.Setting::changeShowingPhone($model->additional_phone_1, 'show_short_clients_phones').'</span></span>' : '')
                . (!empty($model->additional_phone_2) ? '<br/><span class="text-nowrap">Другой <span class="call-phone-button" phone="'.$model->additional_phone_2.'">'.Setting::changeShowingPhone($model->additional_phone_2, 'show_short_clients_phones').'</span></span>' : '')
                . (!empty($model->additional_phone_3) ? '<br/><span class="text-nowrap">Другой <span class="call-phone-button" phone="'.$model->additional_phone_3.'">'.Setting::changeShowingPhone($model->additional_phone_3, 'show_short_clients_phones').'</span></span>' : '');
                //. (in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']) ? '<br />'.intval($client->current_year_places_reliability).'/'.intval($client->current_year_orders_reliability).' - '.intval($client->getPastYearsPlacesReliability()).'/'.intval($client->getPastYearsSendedOrders()) : '');
                //. (in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']) ? '<br />'.$client->getCurrentYear1hReliability().'%/'.$client->getCurrentYear12hOrders().'% - '.$client->getPastYears1hReliability().'%/'.$client->getPastYears12hReliability() : '');

            if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {

                if(empty($client->current_year_sended_orders) && empty($client->current_year_canceled_orders)) {
                    $str .= '<br />NA';
                }else {
                    $str .= '<br />'.$client->getCurrentYear1hRejection().'/'.$client->getCurrentYear12hRejection().'';
                }
                $str .= ' - ';

                if(empty($client->past_years_sended_orders) && empty($client->past_years_canceled_orders)) {
                    $str .= 'NA';
                }else {
                    $str .= $client->getPastYears1hRejection().'/'.$client->getPastYears12hRejection();
                }

//                if($model->accrual_cash_back > 0) {
//                    $str .= '<br />начислено кэш-бэка: ' . $model->accrual_cash_back . '<br />';
//                }
//                if($model->penalty_cash_back > 0) {
//                    $str .= '<br />оштрафовано кэш-бэка: ' . $model->penalty_cash_back . '<br />';
//                }
                if($model->used_cash_back > 0) {
                    $str .= '<br />использовано кэш-бэка для оплаты заказа: ' . $model->used_cash_back . '<br />';
                }
            }


            return $str;
        }
    ],


/*
    [
        'attribute' => 'plan_trip_transport_id',
        'headerOptions' => [
            'title' => 'Планируемое транспортное средство'
        ],
        'contentOptions' => ['column-name' => 'plan-trip-transport-id'],
        'label' => 'План т/с',
        'content' => function($model) use($aTripTransports, $trip, $canceled_order_status_id)
        {
            $trip_transport = $model->planTripTransport;

            $options = [];
//            if(!empty($model->time_sat)) {
//                $options['disabled'] = true;
//            }
            if(!empty($model->time_sat) || !empty($trip->date_sended) || $model->status_id == $canceled_order_status_id) {
                $options['disabled'] = true;
            }

            return PopupFormWidget::widget([
                'name' => 'plan_trip_transport_id',
                'value' => ($trip_transport != null ? $trip_transport->transport->name2 : ''),
                'options' => $options,
                'popupPosition' => 'left',
                'defaultValue' => 'Выберите ТС',
                'formContent' => Html::activeDropDownList($model, 'plan_trip_transport_id', $aTripTransports, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto; margin-right:10px;']),
                'onAccept' => new JsExpression('function(elem, form){
                    var obj = $(this);
                    var plan_trip_transport_id = form.find("#order-plan_trip_transport_id").val();
                    $.ajax({
                        url: "/order/editable-order?id='.$model->id.'",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            plan_trip_transport_id: plan_trip_transport_id
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
                'onCancel' => new JsExpression('function(elem, form) {
                    $.ajax({
                        url: "/order/editable-order?id='.$model->id.'",
                        type: "post",
                        data: {
                            hasEditable: 1,
                            plan_trip_transport_id: ""
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
    ],*/

    [
        'attribute' => 'fact_trip_transport_id',
        'label' => 'Машина',
        'headerOptions' => [
            'title' => 'Фактическое транспортное средство'
        ],
        'contentOptions' => [
            'class' => 'fact-trip-transport',
            'column-name' => 'fact-trip-transport',
        ],
        'content' => function($model) use($aTripTransports, $trip, $canceled_order_status_id)
        {
            if($trip->is_reserv == true) {
                return '';
            }

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
    ],

    [
        'attribute' => 'confirm_selected_transport',
        'headerOptions' => [
            'title' => 'Клиент знает машину'
        ],
        'contentOptions' => ['column-name' => 'confirm-selected-transport'],
        'label' => 'КЗМ?',
        'content' => function($model) use($aTripTransports, $trip, $canceled_order_status_id)
        {
            if($trip->is_reserv == true) {
                return '';
            }

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
    ],

    [
        'attribute' => 'time_sat_without_sort',// Посадить в машину
        'label' => "В т/с",
        'headerOptions' => [
            'style' => 'white-space: normal; width: 40px;',
            'title' => 'Посадить в транстпортное средство'
        ],
        'content' => function($model) use($aOrderStatuses, $trip)
        {
            if($trip->is_reserv == true) {
                return '';
            }

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
                $html = Html::a(
                    '<i class="glyphicon glyphicon-remove"></i>',
                    '#',
                    [
                        'title' => 'Высадить из машины',
                        'class' => "btn btn-danger cancel-put-into-transport",
                    ]
                );

//                if(time() < $trip->date_sended + 6000) {
//                    $html .= Html::a(
//                        '<i class="glyphicon glyphicon glyphicon-road"></i>',
//                        '#',
//                        [
//                            'title' => 'Отправить клиента',
//                            'class' => "btn btn-info send-order",
//                        ]
//                    );
//                }

                return $html;

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
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}',
        'header' => '',
        'buttons' => [
            'update' => function ($url, $model) use($trip) {

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

                    if($trip->date_sended > 0 && time() < $trip->date_sended + 6000 && $model->status_id < 2) {
                        return Html::a(
                            '<i class="glyphicon glyphicon glyphicon-road"></i>',
                            '#',
                            [
                                'title' => 'Отправить клиента в след за т/с',
                                'class' => "btn btn-info send-order-to-trip",
                            ]
                        );
                    }else if($model->status_id == 1) { // created

                        // отображается до отправки машины
//                        return Html::a(
//                            '<i class="glyphicon glyphicon glyphicon-road"></i>',
//                            '#',
//                            [
//                                'title' => 'Отправить клиента в след за т/с2',
//                                'class' => "btn btn-info send-order-to-transport",
//                            ]
//                        );
                    }else {
                        return '';
                    }
                }
            },
        ],
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{delete}',
        'header' => '',
        'buttons' => [
            'delete' => function ($url, $model) use($trip) {
                if(empty($model->time_sat)
                    && $model->status != null
                    && $model->status->code != 'canceled'
                    && empty($trip->date_sended)
                    && $trip->is_reserv == false
                ) {
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
    ],

]);

// $setting = Setting::find()->where(['id' => 1])->one();

if(Yii::$app->setting->show_passenger_button_in_trip_orders_page == true) {

    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{edit_passengers}',
        'header' => '',
        'buttons' => [
            'edit_passengers' => function ($url, $model) use($trip, $aOrdersPassengers) {

                if(empty($trip->date_sended)) {
                    if ($model->places_count == 0) {
                        return '';
                    }

                    if (isset($aOrdersPassengers[$model->id]) && count($aOrdersPassengers[$model->id]) == $model->places_count) {
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
                            //'client-id' => $model->client_id
                        ]
                    );
                }else {
                    return '';
                }
            },
        ],
    ];
}




echo GridView::widget([
    'id' => 'orders-grid',
    'layout' => '{items}',
    'dataProvider' => $orderDataProvider,
    //'filterModel' => $orderSearchModel,
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
            $background_color = '#8b00ff'; // фиолетовый ~ status = 0
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





<div id="bottom-navigation">
    <div class="container">

        <?= Html::a(
            '<i class="glyphicon glyphicon-repeat"></i> Вернуться к списку',
            '/?date='.date('d.m.Y', $trip->date),
            [
                'class' => 'btn btn-warning pull-left',
            ]
        ) ?>

        <?php
        if(Yii::$app->session->get('role_alias') != 'manager' && empty($trip->date_start_sending) && $trip->is_reserv == false) {
            echo Html::a(
                '<i class="glyphicon glyphicon-arrow-right"></i> Начать отправку рейса',
                '#',
                [
                    'id' => 'start-sending-reis',
                    'class' => 'btn btn-success pull-left',
                    'reis-name' => $trip->direction->sh_name.' '.$trip->name
                ]
            );
        }
        ?>

        <?php
        if(!empty($trip->date_start_sending) && empty($trip->date_issued_by_operator)) {
            echo Html::a(
                '<i class="glyphicon glyphicon-arrow-right"></i> Пересчитать цены',
                '#',
                [
                    'id' => 'recount-orders-prices',
                    'class' => 'btn btn-info pull-left',
                ]
            );
        }
        ?>

        <?php
//        if(Yii::$app->session->get('role_alias') != 'manager' && empty($trip->date_sended)) {
//            $options = [
//                'id' => 'send-reis',
//                'class' => 'btn btn-primary pull-left',
//            ];
//            if ($trip->canSend() == false) {
//                $options['disabled'] = true;
//            }
//
//            echo Html::a(
//                '<i class="glyphicon glyphicon-arrow-right"></i> Отправить рейс',
//                '#',
//                $options
//            );
//        }

        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin']) && !empty($trip->date_start_sending) && !empty($trip->date_issued_by_operator) && empty($trip->date_sended)) {
            echo Html::a(
                '<i class="glyphicon glyphicon-arrow-right"></i> Закрыть рейс',
                '#',
                [
                    'id' => 'send-reis',
                    'class' => 'btn btn-primary pull-left',
                ]
            );
        }
        ?>

        <?= Html::a('Печать рейса', ['#'], ['id' => 'print-trip-orders', 'class' => 'btn btn-success pull-right']); ?>

        <?php if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            echo Html::a('Экспортировать в CSV', ['#'], ['id' => 'export-to-csv', 'class' => 'btn btn-info pull-right']);
        } ?>

    </div>
</div>
<br /><br /><br />