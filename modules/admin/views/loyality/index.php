<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;
use kartik\date\DatePicker;
use kartik\export\ExportMenu;

$this->title = 'Лояльность';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerJsFile('js/admin/loyality.js', ['depends'=>'app\assets\AdminAsset']);


$columns = [

    //['class' => 'yii\grid\SerialColumn'],

    //'id',
//    [
//        'attribute' => 'client_id',
//        'content' => function ($model) {
//            return '<a href="/admin/client/update?id='.$model->client_id.'">'.$model->client->name.'</a>';
//        },
//    ],
    [
        'attribute' => 'past_sent_orders',
        'content' => function ($model) {
            return (!empty($model->past_sent_orders) ? $model->past_sent_orders : '');
        },
    ],

    [
        'attribute' => 'past_sent_orders_places',
        'content' => function ($model) {
            return (!empty($model->past_sent_orders_places) ? $model->past_sent_orders_places : '');
        },
    ],

    [
        'attribute' => 'past_canceled_orders',
        'content' => function ($model) {
            return (!empty($model->past_canceled_orders) ? $model->past_canceled_orders : '');
        },
    ],

    [
        'attribute' => 'past_canceled_orders_places',
        'content' => function ($model) {
            return (!empty($model->past_canceled_orders_places) ? $model->past_canceled_orders_places : '');
        },
    ],

    [
        'attribute' => 'past_fixed_price_orders_places',
        'content' => function ($model) {
            return (!empty($model->past_fixed_price_orders_places) ? $model->past_fixed_price_orders_places : '');
        },
    ],

    [
        'attribute' => 'past_is_not_places',
        'content' => function ($model) {
            return (!empty($model->past_is_not_places) ? $model->past_is_not_places : '');
        },
    ],

    [
        'attribute' => 'past_informer_beznal_orders_places',
        'content' => function ($model) {
            return (!empty($model->past_informer_beznal_orders_places) ? $model->past_informer_beznal_orders_places : '');
        },
    ],

    [
        'attribute' => 'past_prize_trip_count',
        'content' => function ($model) {
            return (!empty($model->past_prize_trip_count) ? $model->past_prize_trip_count : '');
        },
    ],

    [
        'attribute' => 'past_penalty',
        'content' => function ($model) {
            return (!empty($model->past_penalty) ? $model->past_penalty : '');
        },
    ],

    [
        'attribute' => 'past_i1',
        'content' => function ($model) {
            return (!empty($model->past_i1) ? $model->past_i1 : '');
        },
    ],
    [
        'attribute' => 'past_i2',
        'content' => function ($model) {
            return (!empty($model->past_i2) ? $model->past_i2 : '');
        },
    ],
    [
        'attribute' => 'past_i3',
        'content' => function ($model) {
            return (!empty($model->past_i3) ? $model->past_i3 : '');
        },
    ],
    [
        'attribute' => 'past_i4',
        'content' => function ($model) {
            return (!empty($model->past_i4) ? $model->past_i4 : '');
        },
    ],
    [
        'attribute' => 'past_i5',
        'content' => function ($model) {
            return (!empty($model->past_i5) ? $model->past_i5 : '');
        },
    ],


    [
        'attribute' => 'present_sent_orders',
        'content' => function ($model) {
            return (!empty($model->present_sent_orders) ? $model->present_sent_orders : '');
        },
    ],

    [
        'attribute' => 'present_sent_orders_places',
        'content' => function ($model) {
            return (!empty($model->present_sent_orders_places) ? $model->present_sent_orders_places : '');
        },
    ],

    [
        'attribute' => 'present_canceled_orders',
        'content' => function ($model) {
            return (!empty($model->present_canceled_orders) ? $model->present_canceled_orders : '');
        },
    ],

    [
        'attribute' => 'present_canceled_orders_places',
        'content' => function ($model) {
            return (!empty($model->present_canceled_orders_places) ? $model->present_canceled_orders_places : '');
        },
    ],

    [
        'attribute' => 'present_fixed_price_orders_places',
        'content' => function ($model) {
            return (!empty($model->present_fixed_price_orders_places) ? $model->present_fixed_price_orders_places : '');
        },
    ],

    [
        'attribute' => 'present_is_not_places',
        'content' => function ($model) {
            return (!empty($model->present_is_not_places) ? $model->present_is_not_places : '');
        },
    ],

    [
        'attribute' => 'present_informer_beznal_orders_places',
        'content' => function ($model) {
            return (!empty($model->present_informer_beznal_orders_places) ? $model->present_informer_beznal_orders_places : '');
        },
    ],

    [
        'attribute' => 'present_prize_trip_count',
        'content' => function ($model) {
            return (!empty($model->present_prize_trip_count) ? $model->present_prize_trip_count : '');
        },
    ],

    [
        'attribute' => 'present_penalty',
        'content' => function ($model) {
            return (!empty($model->present_penalty) ? $model->present_penalty : '');
        },
    ],

    [
        'attribute' => 'present_i1',
        'content' => function ($model) {
            return (!empty($model->present_i1) ? $model->present_i1 : '');
        },
    ],
    [
        'attribute' => 'present_i2',
        'content' => function ($model) {
            return (!empty($model->present_i2) ? $model->present_i2 : '');
        },
    ],
    [
        'attribute' => 'present_i3',
        'content' => function ($model) {
            return (!empty($model->present_i3) ? $model->present_i3 : '');
        },
    ],
    [
        'attribute' => 'present_i4',
        'content' => function ($model) {
            return (!empty($model->present_i4) ? $model->present_i4 : '');
        },
    ],
    [
        'attribute' => 'present_i5',
        'content' => function ($model) {
            return (!empty($model->present_i5) ? $model->present_i5 : '');
        },
    ],


    [
        'attribute' => 'total_sent_orders',
        'content' => function ($model) {
            return (!empty($model->total_sent_orders) ? $model->total_sent_orders : '');
        },
    ],

    [
        'attribute' => 'total_sent_orders_places',
        'content' => function ($model) {
            return (!empty($model->total_sent_orders_places) ? $model->total_sent_orders_places : '');
        },
    ],

    [
        'attribute' => 'total_canceled_orders',
        'content' => function ($model) {
            return (!empty($model->total_canceled_orders) ? $model->total_canceled_orders : '');
        },
    ],

    [
        'attribute' => 'total_canceled_orders_places',
        'content' => function ($model) {
            return (!empty($model->total_canceled_orders_places) ? $model->total_canceled_orders_places : '');
        },
    ],

    [
        'attribute' => 'total_fixed_price_orders_places',
        'content' => function ($model) {
            return (!empty($model->total_fixed_price_orders_places) ? $model->total_fixed_price_orders_places : '');
        },
    ],

    [
        'attribute' => 'total_is_not_places',
        'content' => function ($model) {
            return (!empty($model->total_is_not_places) ? $model->total_is_not_places : '');
        },
    ],

    [
        'attribute' => 'total_informer_beznal_orders_places',
        'content' => function ($model) {
            return (!empty($model->total_informer_beznal_orders_places) ? $model->total_informer_beznal_orders_places : '');
        },
    ],

    [
        'attribute' => 'total_prize_trip_count',
        'content' => function ($model) {
            return (!empty($model->total_prize_trip_count) ? $model->total_prize_trip_count : '');
        },
    ],

    [
        'attribute' => 'total_penalty',
        'content' => function ($model) {
            return (!empty($model->total_penalty) ? $model->total_penalty : '');
        },
    ],

    [
        'attribute' => 'total_i1',
        'content' => function ($model) {
            return (!empty($model->total_i1) ? $model->total_i1 : '');
        },
    ],

    [
        'attribute' => 'total_i2',
        'content' => function ($model) {
            return (!empty($model->total_i2) ? $model->total_i2 : '');
        },
    ],

    [
        'attribute' => 'total_i3',
        'content' => function ($model) {
            return (!empty($model->total_i3) ? $model->total_i3 : '');
        },
    ],

    [
        'attribute' => 'total_i4',
        'content' => function ($model) {
            return (!empty($model->total_i4) ? $model->total_i4 : '');
        },
    ],

    [
        'attribute' => 'total_i5',
        'content' => function ($model) {
            return (!empty($model->total_i5) ? $model->total_i5 : '');
        },
    ],

    'loyalty_indicator',

    //['class' => 'yii\grid\ActionColumn'],
];

?>

<div id="loyality-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">

            <div class="row">
                <div class="col-sm-4 form-group form-group-sm">
                    <?= DatePicker::widget([
                        'id' => 'rewrite-date-from',
                        'name'  => 'from_date',
                        'value'  => '',
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'todayHighlight' => true,
                            'autoclose' => true,
                        ],
                        'options' => [
                            'placeholder' => 'от'
                        ]
                    ]); ?>
                </div>
                <div class="col-sm-4 form-group form-group-sm">
                    <?= DatePicker::widget([
                        'id' => 'rewrite-date-to',
                        'name'  => 'from_date',
                        'value'  => '',
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'todayHighlight' => true,
                            'autoclose' => true,
                        ],
                        'options' => [
                            'placeholder' => 'до'
                        ]
                    ]); ?>
                </div>
                <div class="col-sm-2 form-group form-group-sm">
                    <?= Html::submitButton('Пересчитать данные', ['id' => 'rewrite-loyality', 'class' => 'btn btn-success']) ?>
                </div>
            </div>

        </div>


        <div class="pull-left">
            <?php
            $gridColumns = [
                ['class' => 'kartik\grid\SerialColumn'],

                [
                    'attribute' => 'client_id',
                    'content' => function ($model) {
                        return $model->client->name;
                    },
                ],
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

            [
                'attribute' => 'client_id',
                'content' => function ($model) {
                    return '<a href="/admin/client/update?id='.$model->client_id.'">'.$model->client->name.'</a>';
                },
            ],
        ];
        $gridColumns = array_merge($gridColumns, $columns);

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $gridColumns
        ]); ?>

    </div>
</div>
