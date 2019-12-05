<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\OrderCancellationReason;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\date\DatePicker;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Активность источников (заказы)';
$this->params['breadcrumbs'][] = $this->title;

use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$columns = [
    [
        'attribute' => 'date',
        'label' => 'Дата',
        'content' => function($model) {
            return date('d.m.Y', $model->date);
        },
//        'filter' => DatePicker::widget([
//            'model' => $searchModel,
//            'attribute' => 'date',
//            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//            'pluginOptions' => [
//                'autoclose' => true,
//                'format' => 'dd.mm.yyyy',
//            ]
//        ])
        'filter' => PeriodPicker::widget([
            'model' => $searchModel,
            'attribute' => 'date',
            'isFilterInGridView' => true,
            'clearButton' => false
        ])
    ],
    [
        'attribute' => 'informer_office_id',
        'content' => function($model) {
            if($model->informerOffice != null) {
                return $model->informerOffice->name;
            }else {
                return $model->informer_office_name;
            }
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'informer_office_id',
            ['' => 'Все'] + ArrayHelper::map(\app\models\InformerOffice::find()->all(), 'id', 'name'),
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'fact_trip_transport_id',
        'label' => 'Т/с',
        'content' => function($model) {
            if($model->factTripTransport != null && $model->factTripTransport->transport != null) {
                return $model->factTripTransport->transport->name2;
            }else {
                return $model->fact_trip_transport_model.' ('.$model->fact_trip_transport_car_reg.')';
            }
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'fact_trip_transport_id',
            ['' => 'Все'] + ArrayHelper::map(\app\models\Transport::find()->all(), 'id', 'name2'),
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'client_name',
        'label' => 'Клиент',
    ],
//    [
//        'attribute' => 'street_from_name',
//        'label' => 'Откуда',
//        'content' => function($model) {
//            return $model->street_from_name.' '.$model->point_from_name;
//        }
//    ],
    [
        'attribute' => 'yandex_point_from_name',
        'label' => 'Откуда',
        'content' => function($model) {
            return $model->yandex_point_from_name;
        }
    ],
    [
        'attribute' => 'fact_trip_transport_driver_id',//
        'label' => 'Водитель',
        'content' => function($model) {
            if($model->factTripTransport != null && $model->factTripTransport->driver != null) {
                return $model->factTripTransport->driver->fio;
            }else {
                return $model->fact_trip_transport_driver_fio;
            }
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'fact_trip_transport_driver_id',
            ['' => 'Все'] + ArrayHelper::map(\app\models\Driver::find()->all(), 'id', 'fio'),
            ['class' => "form-control"]
        )
    ],
    [
        'attribute' => 'trip_id',
        'label' => 'Рейс',
        'content' => function($model) {
            if($model->trip != null) {
                return Html::a($model->trip->name, ['/trip/trip-orders', 'trip_id' => $model->trip_id], ['target' => '_blank']);
            }else {
                return $model->trip_name;
            }
        }
    ],
    [
        'attribute' => 'places_count',
        'label' => 'М', //Всего
        'headerOptions' => [
            'title' => 'Мест всего'
        ],
        'content' => function($model) {
            return Html::a(intval($model->places_count), ['/admin/order/view', 'id' => $model->order_id], ['target' => '_blank']);
        }
    ],
];

?>
<div id="rescue-day-print-page" class="box box-default" >
    <div class="box-header scroller with-border">

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
            'filterModel' => $searchModel,
            //'filterModel' => $searchModel,'layout'=>"{summary}\n{items}",
            'layout' => '{summary}{items}{pager}',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $gridColumns
        ]); ?>
    </div>
</div>