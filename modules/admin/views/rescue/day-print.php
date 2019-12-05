<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use app\models\OrderStatus;
use yii\helpers\ArrayHelper;
use app\models\Point;
use app\models\Trip;
use app\models\Order;
use kartik\export\ExportMenu;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;


$this->title = 'Заказы от '.date('d.m.Y', $unixdate);
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

//$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);


$columns = [
    [
        'attribute' => 'direction_id',
        'content' => function($model) {
            if($model->direction != null) {
                return $model->direction->sh_name;
            }else {
                return '';
            }
        },
    ],
    [
        'attribute' => 'trip_id',
        'content' => function($model) {
            if($model->trip != null) {
                return $model->trip->name;
            }else {
                return '';
            }
        },
    ],
//    [
//        'attribute' => 'point_id_from',
//        'content' => function($model) {
//            if($model->point_id_from > 0) {
//                $point = $model->pointFrom;
//                $city = $point->city;
//                return $point->name.' (г.'.$city->name.')';
//            }else {
//                return '';
//            }
//        },
//    ],
    [
        'attribute' => 'yandex_point_from_name',
        'label' => 'Откуда',
        'content' => function($model) {
            return $model->yandex_point_from_name;
        },
    ],
    [
        'attribute' => 'time_confirm',
        'label' => 'ВРПТ',
        'content' => function ($model) {
            return (empty($model->time_confirm) ? '' : date('d.m.Y H:i', $model->time_confirm));
        },
    ],
    [
        'attribute' => 'is_confirmed',
        'label' => 'ПДТ',
        'content' => function ($model) {
            return (empty($model->is_confirmed) ? 'Да' : '');
        },
    ],
//    [
//        'attribute' => 'point_id_to',
//        'content' => function($model) {
//            if($model->point_id_to > 0) {
//                $point = $model->pointTo;
//                if($point == null) {
//                    return '';
//                }
//                $city = $point->city;
//                return $point->name.' (г.'.$city->name.')';
//            }else {
//                return '';
//            }
//        },
//    ],
    [
        'attribute' => 'yandex_point_to_name',
        'label' => 'Куда',
        'content' => function($model) {
            return $model->yandex_point_to_name;
        },
    ],
    [
        'attribute' => 'places_count',
        'label' => 'М/Д/С, БГЖ',
        'content' => function($model) {
            return intval($model->places_count).'/'.intval($model->child_count).'/'.intval($model->student_count).', <br />'
            .intval($model->suitcase_count).'ч, '.intval($model->bag_count).'с, '.intval($model->oversized_count).'н'; // чемоданы, сумки, негабариты
        },
    ],
    [
        'attribute' => 'price',
    ],
    [
        'attribute' => 'client_id',
        'label' => 'ФИО, телефон',
        'content' => function($model) {
            $client = $model->client;
            if($client == null) {
                return '';
            }else {
                return $client->name.' '.$client->mobile_phone;
            }
        },
    ],
    [
        'attribute' => 'fact_trip_transport_id',
        'label' => 'Т/с',
        'content' => function($model) {
            if($model->factTripTransport == null) {
                return '';
            }else {
                return $model->factTripTransport->transport->name4;
            }
        },
    ],
    [
        'attribute' => 'confirm_selected_transport',
        'label' => 'КЗМ?',
        'content' => function($model) {
            return $model->confirm_selected_transport > 0 ? 'Да' : '';
        },
      ],
    [
        'attribute' => 'time_sat',
        'label' => "В т/с",
        'content' => function($model) {
            return $model->time_sat > 0 ? 'Да' : '';
        },
    ],
];
?>
<div id="rescue-day-print-page" class="box box-default" >
    <div class="box-header scroller with-border">

        <div class="pull-left" style="width: 140px; display: inline-block;">
            <?= DatePicker::widget([
                'name' => 'date',
                'value' => date('d.m.Y', $unixdate),
                'removeButton' => false,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'autoclose' => true,
                ],
            ]);
            ?>
        </div>
        <div class="pull-left">
            <?php
            $gridColumns = [
                ['class' => 'kartik\grid\SerialColumn'],
            ];
            $gridColumns = array_merge($gridColumns, $columns);

            echo '<div style="vertical-align: top; display: inline-block;">'.ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'fontAwesome' => true
                ]).'</div>';
            ?>
        </div>

        <div class="pull-left">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination pagination-sm']
            ]); ?>
        </div>
        <?= (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons() ?>

    </div>

    <div></div>

    <div class="box-body box-table">
        <?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
        ];
        $gridColumns = array_merge($gridColumns, $columns);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $gridColumns
        ]); ?>
    </div>
</div>