<?php

use app\models\OrderCancellationReason;
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
use app\models\Direction;
use app\models\Transport;
use app\components\Helper;
use yii\helpers\Url;

$this->registerJsFile('js/site/cancellation-reason-orders.js', ['depends'=>'app\assets\AppAsset']);

//$this->title = 'Перечень заказов за '.$date.', '.Helper::getWeekDay(strtotime($date)).', удаленных со статусом «'.$cancellation_reason->name.'»';
$this->title = 'Перечень удаленных заказов за '.$date.', '.Helper::getWeekDay(strtotime($date)).'.';
$this->params['breadcrumbs'][] = $this->title;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

?>
<div id="cancellation-reason-orders-page">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items}',
        'options' => ['class' => 'grid-view table-responsive'],
        'tableOptions' => [
            'class' => 'table table-condensed table-bordered table-hover'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'client_name',
                'label' => 'ФИО',
                'content' => function ($model) {
                    //return $model->client->name;
                    return '<a target="_blank" href="'.Url::to(['/client/view', 'id' => $model->client_id]).'">'.$model->client->name.'</a>';
                }
            ],
            [
                'attribute' => 'penalty',
                'label' => 'Оштрафовать',
                'content' => function ($model) {
                    return '<button class="btn btn-sm btn-warning btn-penalty '.($model->has_penalty == 1 ? 'disabled' : '').'">Оштафовать</button>';
                },
                'filter' => false
            ],
            [
                'attribute' => 'direction_id',
                'content' => function ($model) {
                    return $model->direction->sh_name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'direction_id',
                    ['' => 'Все'] + ArrayHelper::map(Direction::find()->all(), 'id', 'sh_name'),
                    ['class' => "form-control"]
                )
            ],
            [
                'attribute' => 'trip_id',
                'content' => function($model) {
                    return $model->trip != null ? '<a target="_blank" href="'.Url::to(['/trip/trip-orders', 'trip_id' => $model->trip_id]).'">'.$model->trip->name.'</a>' : '';
                },
            ],
            [
                'attribute' => 'places_student_child_count',
                'label' => 'Состав заказа',
                'content' => function($model) {
                    return '<a target="_blank" href="'.Url::to(['/admin/order', 'OrderSearch[id]' => $model->id]).'">'.$model->places_count.'М, '.$model->student_count.'С, '.$model->child_count.'Д'.'</a>';
                },
                'filter' => false
            ],
//            [
//                'attribute' => 'plan_transport_id',
//                'label' => 'План. т/с',
//                'content' => function($model) {
//                    if($model->plan_trip_transport_id > 0) {
//                        return $model->planTripTransport->transport->name2;
//                    }else {
//                        return '';
//                    }
//                },
//                'filter' => Html::activeDropDownList(
//                    $searchModel,
//                    'plan_transport_id',
//                    ['' => 'Все'] + ArrayHelper::map(Transport::find()->orderBy(['model' => SORT_ASC])->all(), 'id', 'name2'),
//                    ['class' => "form-control"]
//                )
//            ],
//            [
//                'attribute' => 'fact_transport_id',
//                'label' => 'Факт. т/с',
//                'content' => function($model) {
//                    if($model->fact_trip_transport_id > 0) {
//                        return $model->factTripTransport->transport->name2;
//                    }else {
//                        return '';
//                    }
//                },
//                'filter' => Html::activeDropDownList(
//                    $searchModel,
//                    'fact_transport_id',
//                    ['' => 'Все'] + ArrayHelper::map(Transport::find()->where(['active' => 1])->orderBy(['model' => SORT_ASC])->all(), 'id', 'name2'),
//                    ['class' => "form-control"]
//                )
//            ],
            [
                'attribute' => 'cancellation_reason_id',
                'content' => function($model) {
                    if($model->cancellation_reason_id > 0) {
                        return $model->cancellationReason->name;
                    }else {
                        return '';
                    }
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'cancellation_reason_id',
                    ['' => 'Все'] + ArrayHelper::map(OrderCancellationReason::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    ['class' => "form-control"]
                )
            ],
            [
                'attribute' => 'cancellation_click_time',
                'content' => function($model) {
                    if($model->cancellation_click_time > 0) {
                        return date('d.m.Y H:i', $model->cancellation_click_time);
                    }else {
                        return '';
                    }
                },
            ],
            [
                //'attribute' => 'time_vpz',
                'attribute' => 'first_writedown_click_time',
                'label' => 'ВПЗ',
                'headerOptions' => [
                    'title' => ' Время первичной записи'
                ],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
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
                    // return date('d.m.Y H:i', $model->time_vpz);
                    return date('d.m.Y H:i', $model->first_writedown_click_time);
                }
            ],
        ],
    ]); ?>
</div>
