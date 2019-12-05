<?php

use app\models\Trip;
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

$this->title = 'Учет работы водителя';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$aTransportsList = [];
$aTransportsList = ['' => 'Все'];
$transports = Transport::find()->all();
foreach($transports as $transport) {
    $aTransportsList[$transport->id] = $transport->name2;
}

$columns = [

    [
        'attribute' => 'trip_id',
        'label' => 'Рейс',
        'content' => function($model) {
            //return date('d.m.Y H:i', $model->transport_date_sended);
            // {дата рейса} {название рейса} {направление}
            // base_city_trip_id и notbase_city_trip_id -> {$trip->date } {$trip->name} {$trip->direction->sh_name}
            $trip = Trip::find()->where(['id' => $model->trip_id])->one();
            if($trip == null) {
                return 'не найден';
            }else {
                return date("d.m.Y", $trip->date).' '.$trip->end_time.' '.$trip->direction->sh_name;
            }
        },
        'filter' => PeriodPicker::widget([
            'model' => $searchModel,
            'attribute' => 'trip_id',
            'isFilterInGridView' => true,
            'clearButton' => false
        ])
    ],

    [
        'attribute' => 'transport_date_sended',
        'label' => 'ДВО',
        'content' => function($model) {
            return date('d.m.Y H:i', $model->transport_date_sended);
        },
        'filter' => PeriodPicker::widget([
            'model' => $searchModel,
            'attribute' => 'transport_date_sended',
            'isFilterInGridView' => true,
            'clearButton' => false
        ])
    ],

    [
        'label' => 'Водитель',
        'format' => 'raw',
        'attribute' => 'driver_fio',
        'content' => function($model) {
            return Html::a($model->driver_fio, ['/admin/driver', 'DriverSearch[id]' => $model->driver_fio], ['target'=>'_blank']);
        },
        'filter' => SelectWidget::widget([
            'model' => $searchModel,
            'attribute' => 'driver_id',
            'name' => 'driver_id',
            'initValueText' => ($searchModel->driver_id > 0 && $searchModel->driver != null ? $searchModel->driver->fio : ''),
            'options' => [
                'placeholder' => 'Введите ФИО',
            ],
            'ajax' => [
                'url' => '/admin/driver/ajax-get-drivers',
                'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                        };
                    }'),
            ],
            'using_delete_button' => false
        ])
    ],

    [
        'attribute' => 'transport_places_count',
        'label' => 'Мест',
        'content' => function($model) {
            return $model->transport_places_count;
        }
    ],

    [
        'label' => 'ПС', // количество мест по полной стоимости
        'attribute' => 'places_count_sent',
        'content' => function($model) {
            return $model->places_count_sent;
        }
    ],
    [
        'label' => 'С', // Студенты
        'attribute' => 'student_count_sent',
        'content' => function($model) {
            return $model->student_count_sent;
        }
    ],
    [
        'label' => 'Д', // Дети
        'attribute' => 'child_count_sent',
        'content' => function($model) {
            return $model->child_count_sent;
        }
    ],
    [
        'label' => 'Пр', // Призовые поездки
        'attribute' => 'prize_trip_count_sent',
        'content' => function($model) {
            return $model->prize_trip_count_sent;
        }
    ],
    [
        'label' => 'Аэр', // Аэропорт
        'attribute' => 'airport_count_sent',
        'content' => function($model) {
            return $model->airport_count_sent;
        }
    ],
    [
        'label' => 'ФЦ', // фиксированная стоимость
        'attribute' => 'fix_price_count_sent',
        'content' => function($model) {
            return $model->fix_price_count_sent;
        }
    ],
    [
        'label' => 'БМ', // посылки
        'attribute' => 'is_not_places_count_sent',
        'content' => function($model) {
            return $model->is_not_places_count_sent;
        }
    ],
    [
        'label' => 'БЗ', // Без записи
        'attribute' => 'no_record',
        'content' => function($model) {
            return $model->no_record;
        }
    ],
    [
        'label' => 'Выр', // Общая выручка
        'attribute' => 'proceeds',
        'content' => function($model) {
            return number_format($model->proceeds, 2, '.', '').' руб.';
        },
    ],
//    [
//        'label' => 'Т/с',
//        'attribute' => 'transport_id',
//        'content' => function($model) {
//            return $model->transport->name2;
//        },
//        'filter' => Html::activeDropDownList(
//            $searchModel,
//            'transport_id',
//            $aTransportsList,
//            ['class' => "form-control"]
//        )
//    ],
    [
        'label' => 'Т/с',
        'attribute' => 'transport_id',
        'content' => function($model) {
            return $model->transport->name2;
        },
        'filter' => SelectWidget::widget([
            'model' => $searchModel,
            'attribute' => 'transport_id',
            'name' => 'transport_id',
            'initValueText' => ($searchModel->transport_id > 0 && $searchModel->transport != null ? $searchModel->transport->name2 : ''),
            'options' => [
                'placeholder' => 'Введите модель т/с',
            ],
            'ajax' => [
                'url' => '/admin/transport/ajax-get-active-transports',
                'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                        };
                    }'),
            ],
            'using_delete_button' => false
        ])
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
            'columns' => $gridColumns,
        ]); ?>
    </div>
</div>