<?php

use app\models\Order;
use app\models\OrderStatus;
use app\models\YandexPoint;
use app\widgets\EditableTextWidget;
use app\widgets\PopupFormWidget;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;
//use yii2assets\printthis\PrintThis;
use kartik\export\ExportMenu;
use app\helpers\table\PageSizeHelper;

$this->title = 'Печать рейса '.$trip->direction->sh_name.' '.date("d.m.Y", $trip->date).' '.$trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')';

//    /assets/71ced00e/css/bootstrap.css
//    /css/admin/AdminLTE.min.css
//    /css/admin/ionicons.min.css
//    /css/admin/skin-blue.min.css
//    /css/select-widget.css
//    /css/admin/admin.css
//    /css/main.css
//    /css/site_old.css
//    /css/disper.css
//    /css/site.css
//    /css/popup-form-widget.css
//    /css/editable-text-widget.css
//    https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css
//    /assets/1db9ea44/css/kv-grid.css
//    /assets/337abb08/dist/css/bootstrap-dialog.css
//    /assets/1db9ea44/css/jquery.resizableColumns.css
//    /assets/9d3467db/css/daterangepicker.css
//    /assets/9d3467db/css/daterangepicker-kv.css
//    /assets/30bcfb6e/css/kv-widgets.css

//$this->registerCssFile('/assets/71ced00e/css/bootstrap.css');
//$this->registerCssFile("@web/css/es/all.css",  ['depends' => [BootstrapAsset::className()]]);
$this->registerCssFile('/css/site.css');

//echo PrintThis::widget([
//    'htmlOptions' => [
//        'id' => 'print-trip-orders-page',
//        'btnClass' => 'btn btn-info',
//        'btnId' => 'btnPrintThis',
//        'btnText' => 'Печать',
//        'btnIcon' => 'fa fa-print'
//    ],
//    'options' => [
//        'debug' => false,
//        'importCSS' => true,
//        'importStyle' => false,
//        //'loadCSS' => "path/to/my.css",
//        'pageTitle' => "",
//        'removeInline' => false,
//        'printDelay' => 333,
//        'header' => null,
//        'formValues' => true,
//    ]
//]);




$aYandexPointList = ArrayHelper::map(YandexPoint::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
$aOrderStatuses = ArrayHelper::map(OrderStatus::find()->all(), 'id', 'code');

$canceled_order_status_id = array_flip($aOrderStatuses)['canceled'];

$trip_orders = Order::find()
    ->where(['trip_id' => $trip->id])
    ->andWhere(['!=', 'status_id', $canceled_order_status_id])
    ->all();

$trip_price = 0;
$confirmed_places = 0;
$trip_orders_places_count = 0;
foreach($trip_orders as $trip_order) {
    $trip_price += $trip_order->price;
    $trip_orders_places_count += $trip_order->places_count;
    if($trip_order->is_confirmed == 1) {
        $confirmed_places += $trip_order->places_count;
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
        if(empty($trip_transport->date_sended)) {
            $aTripTransports[$trip_transport->id] = $transport->name4;
        }
    }
}

$user = Yii::$app->user->identity;
$user_role_alias = $user->userRole->alias;
?>

<style type="text/css">
    #orders-grid table > thead > tr > th, #orders-grid .table > tbody > tr > th, #orders-grid .table > tfoot > tr > th, #orders-grid .table > thead > tr > td, #orders-grid .table > tbody > tr > td, #orders-grid .table > tfoot > tr > td {
        padding: 0 5px;
    }
</style>

<?php
$transportColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute' => 'transport_car_reg',
        'label' => 'Номер',
        'content' => function($model) {
            $transport = $model->transport;
            return ($transport == null ? '' : '<a href="#" class="trip_transport" trip_transport_id="'.$model->id.'">'.$transport->car_reg.'</a>');
        },
        'enableSorting' => false
    ],
    [
        'attribute' => 'transport_model',
        'label' => 'Модель',
        'content' => function($model) {
            $transport = $model->transport;
            return ($transport == null ? '' : $transport->model.' '.$transport->color);
        },
        'enableSorting' => false
    ],
];

$transportColumns[] = [
    'attribute' => 'transport_places_count',
    'headerOptions' => ['style' => 'white-space: normal; width: 190px;'],
    'label' => 'Запланировано / Знает номер / Кол-во мест',
    'content' => function($model) {
        $transport = $model->transport;
        if($transport == null) {
            return '0/0/0';
        }else {
            return $model->factPlacesCount.'/'.$model->factKZMPlacesCount.'/'.$transport->places_count;
        }
    },
    'enableSorting' => false
];

$transportColumns[] = [
    'attribute' => 'driver_id',
    'label' => 'Водитель',
    'content' => function($model) {
        $driver = $model->driver;
        return ($driver == null ? '' : $driver->fio);
    },
    'enableSorting' => false
];

$transportColumns[] = [
    'attribute' => 'status_id',
    'label' => 'Статус',
    'content' => function($model) {
        return $model->statusName;
    },
    'enableSorting' => false
];




$orderColumns = [];
$printOrderColumns = [];

if(empty($trip->date_sended)) {

//    $orderColumns[] = [
//        'header' => '<input class="select-on-check-all" name="selection_all" value="1" type="checkbox">',
//        'content' => function($model, $key, $index, $column) use($canceled_order_status_id) {
//            if(empty($model->time_sat) && $model->status_id != $canceled_order_status_id) {
//                return '<input name="selection[]" value="'.$key.'" type="checkbox">';
//            }else {
//                return '';
//            }
//        }
//    ];
}




$orderColumns[] = [

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

            if(empty($model->yandex_point_from_name)) {
                return "<br /><br />";
            }else {
                return $model->yandex_point_from_name;
            }
        }

    },
    'filter' => Html::activeDropDownList(
        $orderSearchModel,
        'yandex_point_from_id',
        ['' => 'Все'] + $aYandexPointList,
        ['class' => "form-control"]
    ),
    'enableSorting' => false
];
$printOrderColumns[] = [

    'attribute' => 'yandex_point_from_id',
    'label' => "Откуда",
    'content' => function($model) {

        $yandexPoint = $model->yandexPointFrom;
        if($yandexPoint != null && $yandexPoint->critical_point == 1) { // добавляем жирный шрифт
            if(!empty($model->time_air_train_arrival)) { // добавляем красный цвет
                return $model->yandex_point_from_name.', '.$model->time_air_train_arrival;
            }else { // просто отображается жирный шрифт обычного цвета
                return $model->yandex_point_from_name;
            }
        }else { // отображаем обычный шрифт

            if(empty($model->yandex_point_from_name)) {
                return "\n";
            }else {
                return $model->yandex_point_from_name;
            }
        }

    }
];





$orderColumns[] = [
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
            return '<span class="time-confirm-sort">' . $model->time_confirm_sort . '</span>';
        }
    },
    'enableSorting' => false
];

$time_confirm = [
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

        return ($model->time_confirm > 0 ? date('H:i', $model->time_confirm) : '');
    },
    'enableSorting' => false
];
$orderColumns[] = $time_confirm;
$printOrderColumns[] = $time_confirm;

$orderColumns[] = [
    'attribute' => 'is_confirmed_without_sort',
    'label' => 'ПДТ',
    'headerOptions' => [
        'title' => 'Подтвердить'
    ],
    'content' => function ($model) use($canceled_order_status_id) {

        if(!empty($model->is_confirmed)) {
            return 'ПДТ';
        }
    },
    'enableSorting' => false
];

$orderColumns[] = [
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
    ),
    'enableSorting' => false
];

$printOrderColumns[] =  [
    'attribute' => 'yandex_point_to_id',
    'label' => "Куда",
    'content' => function($model) {
        if($model->yandex_point_to_id > 0) {
            $yandex_point_to = $model->yandexPointTo;

            if($yandex_point_to->critical_point == 1) { // добавляем жирный шрифт
                if(!empty($model->time_air_train_departure)) { // добавляем красный цвет
                    return $model->yandex_point_to_name.', '.$model->time_air_train_departure;
                }else { // просто отображается жирный шрифт обычного цвета
                    return $model->yandex_point_to_name;
                }
            }else { // отображаем обычный шрифт
                return $model->yandex_point_to_name;
            }
        }else {
            return $model->yandex_point_to_name;
        }
    },
];



$orderColumns[] = [
    'attribute' => 'places_count',
    'headerOptions' => [
        'title' => 'Мест всего'
    ],
    'contentOptions' => function($model) {
        return [
            'class' => 'places-count',
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

        return $value;
    },
    'enableSorting' => false
];
$printOrderColumns[] = [
    'attribute' => 'places_count',
    'headerOptions' => [
        'title' => 'Мест всего'
    ],
    'contentOptions' => function($model) {
        return [
            'class' => 'places-count',
            'places-count' => $model->places_count
        ];
    },
    'label' => 'М',
    'content' => function($model) use($user_role_alias) {
        if($model->is_not_places == 1) {
            $value = '–';
        }else {
            $value = $model->places_count;
        }

        return $value;
    },
    'enableSorting' => false
];


$child_count = [
    'attribute' => 'child_count',
    'headerOptions' => [
        'title' => 'Детский мест'
    ],
    'label' => 'Д',
    'content' => function($model) {
        if($model->places_count == 0) {
            return '';
        }else {
            return intval($model->child_count);
        }
    },
    'enableSorting' => false
];
$orderColumns[] = $child_count;
$printOrderColumns[] = $child_count;


$student_count = [
    'attribute' => 'student_count',
    'headerOptions' => [
        'title' => 'Студенческих мест'
    ],
    'label' => 'С',
    'content' => function($model) {
        if($model->places_count == 0) {
            return '';
        }else {
            return intval($model->student_count);
        }
    },
    'enableSorting' => false
];
$orderColumns[] = $student_count;
$printOrderColumns[] = $student_count;

$bag_suitcase_oversized_count = [
    'attribute' => 'bag_suitcase_oversized_count',
    'label' => 'Багаж',
    'headerOptions' => [
        'title' => 'Количество чемоданов, сумок, негабаритов'
    ],
    'content' => function($model) {
        if($model->places_count == 0) {
            return '';
        }else {
            return intval($model->suitcase_count) . 'Ч, ' . intval($model->bag_count) . 'С, ' . intval($model->oversized_count) . 'Н'; // чемоданы, сумки, негабариты
        }
    },
    'enableSorting' => false
];
$orderColumns[] = $bag_suitcase_oversized_count;
$printOrderColumns[] = $bag_suitcase_oversized_count;



$orderColumns[] = [

    'attribute' => 'comment',
    'label' => 'Примечания',
    'content' => function($model) use($canceled_order_status_id) {
        $str = '';

        if(!empty($model->comment)) {
            $str .= '<span>' . $model->comment . '</span><hr style="margin: 5px 0;" />';
        }
        $str .= $model->price > 0 ? 'Стоимость: <b>'.$model->price.'</b>' : '';
        if($model->informer_office_id > 0) {
            $str .= ', <i>сброс '.$model->informerOffice->name.'</i>';
        }
        if($model->relation_order_id > 0) {
            $relation_order = Order::findOne($model->relation_order_id);
            $client = $relation_order->client;
            $str .= '<br />Едет совместно с '.$client->name.' '.$client->mobile_phone;
        }

        if($model->status_id == $canceled_order_status_id && $model->cancellation_click_time > 0) {
            $str .=
                '<br />Удалено в '.date('d.m.Y H:i', $model->cancellation_click_time)
                .($model->cancellationClicker != null ? ' оператором '.$model->cancellationClicker->fullname : '')
                .'<br />('.$model->cancellationReason->name.')';
        }

        return $str;
    },
    'enableSorting' => false
];
$printOrderColumns[] = [
    'attribute' => 'comment',
    'label' => 'Примечания',
    'content' => function($model) use($canceled_order_status_id) {
        $str = '';

        if(!empty($model->comment)) {
            $str .= $model->comment."\n";
        }
        $str .= $model->price > 0 ? 'Стоимость: '.$model->price.'' : '';
        if($model->informer_office_id > 0) {
            $str .= ', сброс '.$model->informerOffice->name;
        }
        if($model->relation_order_id > 0) {
            $relation_order = Order::findOne($model->relation_order_id);
            $client = $relation_order->client;
            $str .= 'Едет совместно с '.$client->name.' '.$client->mobile_phone;
        }

        if($model->status_id == $canceled_order_status_id && $model->cancellation_click_time > 0) {
            $str .=
                "\nУдалено в ".date('d.m.Y H:i', $model->cancellation_click_time)
                .($model->cancellationClicker != null ? ' оператором '.$model->cancellationClicker->fullname : '')
                ."\n(".$model->cancellationReason->name.')';
        }

        return $str;
    },
    'enableSorting' => false
];


$orderColumns[] = [
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
        // return $model->time_vpz > 0 ? date('d.m.Y H:i', $model->time_vpz) : '';
        return $model->first_writedown_click_time > 0 ? date('d.m.Y H:i', $model->first_writedown_click_time) : '';
    },
    'enableSorting' => false
];

$orderColumns[] = [
    'attribute' => 'client_name_phone',
    'label' => 'Клиент',
    'headerOptions' => [
        'style' => 'max-width: 150px;'
    ],
    'content' => function ($model) {
        $client = $model->client;
        if($client == null) {
            return '';
        }else {
            return
                $client->name
                . '<br /><span class="text-nowrap">М. ' . $client->mobile_phone
                . (!empty($client->home_phone) ? '<br /> Д. ' . $client->home_phone : '') . '</span>'
                . (!empty($client->alt_phone) ? '<br/><span class="text-nowrap">Другой ' . $client->alt_phone : '')
                . (!empty($model->additional_phone_1) ? '<br/><span class="text-nowrap">Другой ' . $model->additional_phone_1 . '</span>' : '')
                . (!empty($model->additional_phone_2) ? '<br/><span class="text-nowrap">Другой ' . $model->additional_phone_2 . '</span>' : '')
                . (!empty($model->additional_phone_3) ? '<br/><span class="text-nowrap">Другой ' . $model->additional_phone_3 . '</span>' : '');
        }
    },
    'enableSorting' => false
];
$printOrderColumns[] = [
    'attribute' => 'client_name_phone',
    'label' => 'Клиент',
    'headerOptions' => [
        'style' => 'max-width: 150px;'
    ],
    'content' => function ($model) {
        $client = $model->client;
        if($client == null) {
            return '';
        }else {
            return
                $client->name
                . "\nМ. " . $client->mobile_phone
                . (!empty($client->home_phone) ? "\n Д. " . $client->home_phone : '')
                . (!empty($client->alt_phone) ? "\nДругой " . $client->alt_phone : '')
                . (!empty($model->additional_phone_1) ? "\nДругой " . $model->additional_phone_1 : '')
                . (!empty($model->additional_phone_2) ? "\nДругой " . $model->additional_phone_2 : '')
                . (!empty($model->additional_phone_3) ? "\nДругой " . $model->additional_phone_3 : '');
        }
    },
    'enableSorting' => false
];

$fact_trip_transport_id = [
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
        $trip_transport = $model->factTripTransport;

        $options = [];
        if(!empty($model->time_sat) || !empty($trip->date_sended) || $model->status_id == $canceled_order_status_id) {
            $options['disabled'] = true;
        }

        return $trip_transport != null ? $trip_transport->transport->name4 : '';
    },
    'enableSorting' => false
];
$orderColumns[] = $fact_trip_transport_id;
$printOrderColumns[] = $fact_trip_transport_id;


$confirm_selected_transport = [
    'attribute' => 'confirm_selected_transport',
    'headerOptions' => [
        'title' => 'Клиент знает машину'
    ],
    'contentOptions' => ['column-name' => 'confirm-selected-transport'],
    'label' => 'КЗМ?',
    'content' => function($model) use($aTripTransports, $trip, $canceled_order_status_id)
    {
        if($model->confirm_selected_transport == 1) {
            return 'да';
        }else {
            return '';
        }
    },
    'enableSorting' => false
];
$orderColumns[] = $confirm_selected_transport;
$printOrderColumns[] = $confirm_selected_transport;

//$orderColumns = ArrayHelper::merge($orderColumns, [
//
//]);


/*
<script type="text/javascript">
    function print_doc(){
        window.print() ;
    }
</script>
<button id="print" onclick="javascript:print_doc()">Печать</button>
*/

?>


<div id="print-trip-orders-page">

    <?php
    // Откуда, ВРПТ, Куда, МДС, Багаж, Примечания, Клиент, Машина, КЗМ?
    echo
        ExportMenu::widget([
            'dataProvider' => $orderDataProvider,
            'columns' => $printOrderColumns,
            'fontAwesome' => true
        ]);
    ?>
    <br /><br />

    <div id="print-trip-orders-page-title">
        <b>РЕЙС <?= $trip->direction->sh_name.' '.$trip->name.' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.') от '.date("d.m.Y", $trip->date) ?></b>
    </div>
    <br />
    <div>
        <div id="reis-panel-left">
            <span id="trip-price">ОСР - <?= $trip_price ?></span>
            <span class="places-count pull-right">
                <span title="Подтвержденных мест" class="text-green"><?= $confirmed_places ?></span>/<span title="Всего мест"><?= $trip_orders_places_count ?></span>/<span title="Удаленных мест" class="text-red"><?= $deleted_count ?></span>/<span title="Мест в т/с"><?= $count_transports_places ?></span>
            </span>
        </div>
    </div>

<?php
    echo GridView::widget([
        'id' => 'trip-transports-grid',
        'dataProvider' => $transportDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {

            if(!empty($model->date_sended)) { // отправлен - синий
                $background_color = '#92B9F4';// 256DFF
            }else {
                if($model->confirmed == 1) { // подтвержден - зеленый
                    $background_color = '#E6FFE6';
                }else {     // не подтвержден - желтый
                    $background_color = '#FFF9B4';
                }
            }

            return [
                'style' => 'background-color: '.$background_color.';',
            ];
        },
        'layout' => '{items}',
        'options' => [
            'class' => 'grid-view table-responsive',
            'style' => 'margin-top: 30px; width: 100%; '
        ],
        'tableOptions' => [
            'class' => 'table table-condensed table-bordered table-hover'
        ],
        'columns' => $transportColumns
    ]); ?>

    <br />
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


<?php

echo GridView::widget([
    'id' => 'orders-grid',
    'tableOptions' => [
        //'cellpadding' => 0,
        //'cellspacing' => 0,
        //'border' => 1
    ],
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
        }

        return [
            'style' => 'background-color: '.$background_color.';',//  cellpadding:0 ; cellspacing:0;
            'order-id' => $model->id,
            'order-status' => $model->status == null ? '' : $model->status->code,
        ];
    },
    'columns' => $orderColumns,
]); ?>

    <br /><br /><br />
</div>