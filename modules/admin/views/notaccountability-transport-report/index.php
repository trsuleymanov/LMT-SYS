<?php

use app\helpers\table\PageSizeHelper;
use app\widgets\periodPicker\PeriodPicker;
use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\LinkPager;


$this->title = 'Сдача процентов (для неподотчетных машин)';
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

                // 'id',

                [
                    'attribute' => 'date_end_circle',
                    'label' => 'Период',
                    'content' => function ($model) {
                        return date('d.m.Y', $model->date_start_circle).' - '.date('d.m.Y', $model->date_end_circle);
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date_end_circle',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

                [
                    'attribute' => 'transport_id',
                    'content' => function ($model) {
                        return ($model->transport != null ? $model->transport->name2 : '');
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
                        //'using_delete_button' => false
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
                    'attribute' => 'trip_transport_start',
                    'content' => function ($model) {
                        if($model->tripTransportStart != null && $model->tripTransportStart->trip != null) {
                            $trip = $model->tripTransportStart->trip;
                            return ($trip->direction_id == 1 ? 'АК ' : 'КА ').$trip->name;
                        }else {
                            return '';
                        }
                    },
                    'filter' => false
                ],
                [
                    'attribute' => 'trip_transport_end',
                    'content' => function ($model) {
                        if($model->tripTransportEnd != null && $model->tripTransportEnd->trip != null) {
                            $trip = $model->tripTransportEnd->trip;
                            return ($trip->direction_id == 1 ? 'АК ' : 'КА ').$trip->name;
                        }else {
                            return '';
                        }
                    },
                    'filter' => false
                ],
                [
                    'attribute' => 'hand_over',
                    'content' => function ($model) {
                        return ($model->hand_over > 0 ? $model->hand_over : '');
                    },
                ],
                [
                    'attribute' => 'formula_percent',
                    'label' => 'Сдано процентов',
                    'content' => function ($model) {
                        return ($model->formula_percent > 0 ? $model->formula_percent : '');
                    },
                ],
                [
                    'attribute' => 'set_hand_over_time',
                    'content' => function ($model) {
                        return ($model->set_hand_over_time > 0 ? date('d.m.Y', $model->set_hand_over_time) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'set_hand_over_time',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'set_hand_over_operator_id',
                    'content' => function ($model) {
                        return ($model->set_hand_over_operator_id > 0 ? $model->handOverOperator->fio : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'set_hand_over_operator_id',
                        'name' => 'set_hand_over_operator_id',
                        'value' => ($searchModel->set_hand_over_operator_id > 0 && $searchModel->handOverOperator != null ? $searchModel->handOverOperator->fio : ''),
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


                // ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
