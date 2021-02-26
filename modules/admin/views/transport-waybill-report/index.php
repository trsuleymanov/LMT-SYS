<?php

use app\helpers\table\PageSizeHelper;
use app\widgets\periodPicker\PeriodPicker;
use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\LinkPager;


$this->title = 'Сдача выручки (для подотчетных машин)';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

?>
<div id="notaccountability-transport-report-index" class="box box-default">


    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination pagination-sm']
            ]); ?>
        </div>
        <?= (new PageSizeHelper([10, 20, 50, 100, 200, 500]))->getButtons() ?>
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                [
                    'attribute' => 'number',
                    'label' => '№ПЛ',
                ],
                [
                    'attribute' => 'date_of_issue',
                    'label' => 'ДВ ПЛ',
                    'content' => function ($model) {
                        return date('d.m.Y', $model->date_of_issue);
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_of_issue',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

                [
                    'attribute' => 'driver_id',
                    'content' => function ($model) {
                        return ($model->driver != null ? $model->driver->fio : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'driver_id',
                        'name' => 'driver_id',
                        'value' => ($searchModel->driver_id > 0 && $searchModel->driver != null ? $searchModel->driver->fio : ''),
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
                        'using_delete_button' => true
                    ])
                ],

                [
                    'attribute' => 'transport_id',
                    'content' => function ($model) {
                        return ($model->transport != null ? $model->transport->name2 : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'driver_id',
                        'name' => 'driver_id',
                        'value' => ($searchModel->transport > 0 && $searchModel->transport != null ? $searchModel->transport->name2 : ''),
                        'options' => [
                            'placeholder' => 'Введите т/с',
                        ],
                        'ajax' => [
                            'url' => '/admin/transport/ajax-get-active-transports',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],

//                [
//                    'attribute' => 'trip_transport_start',
//                    'content' => function ($model) {
//                        if($model->tripTransportStart != null && $model->tripTransportStart->trip != null) {
//                            $trip = $model->tripTransportStart->trip;
//                            return ($trip->direction_id == 1 ? 'АК ' : 'КА ').$trip->name;
//                        }else {
//                            return '';
//                        }
//                    },
//                ],
//                [
//                    'attribute' => 'trip_transport_end',
//                    'content' => function ($model) {
//                        if($model->tripTransportEnd != null && $model->tripTransportEnd->trip != null) {
//                            $trip = $model->tripTransportEnd->trip;
//                            return ($trip->direction_id == 1 ? 'АК ' : 'КА ').$trip->name;
//                        }else {
//                            return '';
//                        }
//                    },
//                ],
                [
                    'attribute' => 'hand_over_b1',
                    'label' => 'B1',
                    'content' => function ($model) {
                        return ($model->hand_over_b1_data > 0 ? $model->hand_over_b1 : '');
                    },
                ],
                [
                    'attribute' => 'hand_over_b1_data',
                    'label' => 'Дата сдачи В1',
                    'content' => function ($model) {
                        return ($model->hand_over_b1_data > 0 ? date('d.m.Y', $model->hand_over_b1_data) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'hand_over_b1_data',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'set_hand_over_b1_operator_id',
                    'label' => 'Оператор В1',
                    'content' => function ($model) {
                        return ($model->set_hand_over_b1_operator_id > 0 ? $model->handOverB1Operator->fio : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'set_hand_over_b1_operator_id',
                        'name' => 'set_hand_over_b1_operator_id',
                        'value' => ($searchModel->set_hand_over_b1_operator_id > 0 && $searchModel->handOverB1Operator != null ? $searchModel->handOverB1Operator->fio : ''),
                        'options' => [
                            'placeholder' => 'Введите ФИО',
                        ],
                        'ajax' => [
                            'url' => '/user/ajax-get-users',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],
//                [
//                    'attribute' => 'set_hand_over_b1_time',
//                    'content' => function ($model) {
//                        return ($model->set_hand_over_b1_time > 0 ? date('d.m.Y', $model->set_hand_over_b1_time) : '');
//                    },
//                    'filter' => PeriodPicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'set_hand_over_b1_time',
//                        'isFilterInGridView' => true,
//                        'clearButton' => false
//                    ])
//                ],
                [
                    'attribute' => 'hand_over_b2',
                    'label' => 'B2',
                    'content' => function ($model) {
                        return ($model->hand_over_b2_data > 0 ? $model->hand_over_b2 : '');
                    },
                ],
                [
                    'attribute' => 'hand_over_b2_data',
                    'label' => 'Дата сдачи В2',
                    'content' => function ($model) {
                        return ($model->hand_over_b2_data > 0 ? date('d.m.Y', $model->hand_over_b2_data) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'hand_over_b2_data',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'set_hand_over_b2_operator_id',
                    'label' => 'Оператор В2',
                    'content' => function ($model) {
                        return ($model->set_hand_over_b2_operator_id > 0 ? $model->handOverB2Operator->fio : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'set_hand_over_b2_operator_id',
                        'name' => 'set_hand_over_b2_operator_id',
                        'value' => ($searchModel->set_hand_over_b2_operator_id > 0 && $searchModel->handOverB2Operator != null ? $searchModel->handOverB2Operator->fio : ''),
                        'options' => [
                            'placeholder' => 'Введите ФИО',
                        ],
                        'ajax' => [
                            'url' => '/user/ajax-get-users',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],
//                [
//                    'attribute' => 'set_hand_over_b2_time',
//                    'content' => function ($model) {
//                        return ($model->set_hand_over_b2_time > 0 ? date('d.m.Y', $model->set_hand_over_b2_time) : '');
//                    },
//                    'filter' => PeriodPicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'set_hand_over_b2_time',
//                        'isFilterInGridView' => true,
//                        'clearButton' => false
//                    ])
//                ],

//                [
//                    'attribute' => 'total_failure_to_pay',
//                    'content' => function ($model) {
//                        return ($model->total_failure_to_pay > 0 ? $model->total_failure_to_pay : 0);
//                    },
//                ],

                // ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
