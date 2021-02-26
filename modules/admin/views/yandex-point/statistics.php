<?php

use app\models\City;
use app\models\Trip;
use app\models\YandexPoint;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\DispatcherAccounting;
use app\models\User;
use app\widgets\periodPicker\PeriodPicker;
use app\models\Transport;
use app\models\Driver;
use yii\helpers\ArrayHelper;
use kartik\export\ExportMenu;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;
use app\widgets\SelectWidget;
use yii\web\JsExpression;

$this->title = 'Статистика по точкам';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();





$columns = [
    [
        // здесь нужно искать по датам рейсов, шаг поиска - 1 день, поэтому поле trip.date подходит
        'attribute' => 'trip_date',
        'label' => 'Дата рейса',
        'content' => function($model) use($params) {
            if(isset($params['YandexPointSearch']['trip_date']) && !empty($params['YandexPointSearch']['trip_date'])) {
                return $params['YandexPointSearch']['trip_date'];
            }else {
                return 'за всё время';
            }
        },
        'filter' => PeriodPicker::widget([
            'model' => $searchModel,
            'attribute' => 'trip_date',
            'isFilterInGridView' => true,
            'clearButton' => false
        ])
    ],
    [
        'label' => 'Название',
        'attribute' => 'name',
        'content' => function($point){
            return $point->name;
        },
    ],
    [
        'attribute' => 'id',
    ],
    [
        'attribute' => 'city_id',
        'label' => 'Город',
        'content' => function($point) use($aCities)  {
            return (isset($aCities[$point->city_id]) ? $aCities[$point->city_id]->name : '');
        },
        'filter' => Html::activeDropDownList(
            $searchModel,
            'city_id',
            ['' => 'Все'] + ArrayHelper::map($cities, 'id', 'name'),
            ['class' => "form-control"]
        )
    ],

//    [
//        'attribute' => 'orders_total_count',
//        'label' => 'Всего заказов',
//        'content' => function($point) use($aTotalCounts)  {
//            return (isset($aTotalCounts[$point->id]) ? $aTotalCounts[$point->id] : 0);
//        },
//        'filter' => false
//    ],
//    [
//        'attribute' => 'orders_total_count',
//        'label' => 'Пас. в заказах',
//        'content' => function($point) use($aTotalPlacesCount)  {
//            return (isset($aTotalPlacesCount[$point->id]) ? $aTotalPlacesCount[$point->id] : 0);
//        },
//        'filter' => false
//    ],


    [
        'attribute' => 'orders_sended_count',
        'label' => 'Заказов ОТПР',
        'content' => function($point) use($aSendedCounts)  {
            return (isset($aSendedCounts[$point->id]) ? $aSendedCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'places_sended_count',
        'label' => 'Пасс. в ОТПР заказах',
        'content' => function($point) use($aSendedPlacesCount)  {
            return (isset($aSendedPlacesCount[$point->id]) ? $aSendedPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'orders_canceled_count',
        'label' => 'Заказов ОТМ',
        'content' => function($point) use($aCanceledCounts)  {
            return (isset($aCanceledCounts[$point->id]) ? $aCanceledCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'places_canceled_count',
        'label' => 'Пасс. в ОТМ заказах',
        'content' => function($point) use($aCanceledPlacesCount)  {
            return (isset($aCanceledPlacesCount[$point->id]) ? $aCanceledPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'empty',
        'label' => '',
        'content' => function($point) use($aCanceledPlacesCount)  {
            return '';
        },
        'filter' => false
    ],

    [
        'attribute' => 'child_orders_sended_count',
        'label' => 'Заказов с Д ОТПР',
        'content' => function($point) use($aChildrenSendedCounts)  {
            return (isset($aChildrenSendedCounts[$point->id]) ? $aChildrenSendedCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_places_sended_count',
        'label' => 'Пасс в ОТПР заказах с Д',
        'content' => function($point) use($aChildrenSendedPlacesCount)  {
            return (isset($aChildrenSendedPlacesCount[$point->id]) ? $aChildrenSendedPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_orders_canceled_count',
        'label' => 'Заказов ОТМ с Д',
        'content' => function($point) use($aChildrenCanceledCounts)  {
            return (isset($aChildrenCanceledCounts[$point->id]) ? $aChildrenCanceledCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_places_canceled_count',
        'label' => 'Пасс в ОТМ заказах с Д',
        'content' => function($point) use($aChildrenCanceledPlacesCount)  {
            return (isset($aChildrenCanceledPlacesCount[$point->id]) ? $aChildrenCanceledPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'empty2',
        'label' => '',
        'content' => function($point) use($aCanceledPlacesCount)  {
            return '';
        },
        'filter' => false
    ],


    [
        'attribute' => 'child_orders_sended_count',
        'label' => 'Заказов с СТ ОТПР',
        'content' => function($point) use($aStudentsSendedCounts)  {
            return (isset($aStudentsSendedCounts[$point->id]) ? $aStudentsSendedCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_places_sended_count',
        'label' => 'Пасс в ОТПР заказах с СТ',
        'content' => function($point) use($aStudentsSendedPlacesCount)  {
            return (isset($aStudentsSendedPlacesCount[$point->id]) ? $aStudentsSendedPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_orders_canceled_count',
        'label' => 'Заказов с СТ ОТМ',
        'content' => function($point) use($aStudentsCanceledCounts)  {
            return (isset($aStudentsCanceledCounts[$point->id]) ? $aStudentsCanceledCounts[$point->id] : 0);
        },
        'filter' => false
    ],
    [
        'attribute' => 'child_places_canceled_count',
        'label' => 'Пасс в ОТМ заказах с СТ',
        'content' => function($point) use($aStudentsCanceledPlacesCount)  {
            return (isset($aStudentsCanceledPlacesCount[$point->id]) ? $aStudentsCanceledPlacesCount[$point->id] : 0);
        },
        'filter' => false
    ],
];


?>
<div id="dispatcher-accounting-index" class="box box-default" >


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
            <?php
//            echo LinkPager::widget([
//                'pagination' => $pagination,
//                'options' => ['class' => 'pagination pagination-sm']
//            ]);
            ?>
        </div>
        <?php //= (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons() ?>
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
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $gridColumns,
        ]); ?>
    </div>
</div>