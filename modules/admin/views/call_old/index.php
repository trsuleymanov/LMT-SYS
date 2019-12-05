<?php

use app\models\Call;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;
use yii\helpers\Html;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Звонки';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="tariff-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left"></div>

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
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'client_phone',
                    'content' => function ($model) {
                        return !empty($model->client_phone) ? $model->client_phone : '';
                    }
                ],
                [
                    'attribute' => 'handling_call_operator_id',
                    'content' => function ($model) {
                        return !empty($model->handling_call_operator_id) && $model->handlingCallOperator != null ? $model->handlingCallOperator->username : '';
                    },
                    'filter' => false
                ],
                [
                    'attribute' => 'call_from_operator',
                    'label' => 'Направление звонка',
                    'content' => function ($model) {
                        return $model->call_from_operator == 1 ? 'Исходящий' : 'Входящий';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'call_from_operator',
                        ['' => 'Все', 0 => 'Входящие', 1 => 'Исходящие'],
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'status',
                    'content' => function ($model) {
                        return (!empty($model->status) ? Call::getStatuses()[$model->status] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status',
                        ['' => 'Все'] + Call::getStatuses(),
                        ['class' => "form-control"]
                    )
                ],

//                [
//                    'label' => 'Начало звонка (в АТС)',
//                    'attribute' => 'ats_start_time',
//                    'content' => function ($model) {
//                        return (!empty($model->ats_start_time) ? date('d.m.Y H:i:s', $model->ats_start_time/1000) : '');
//                    },
//                    'filter' => PeriodPicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'ats_start_time',
//                        'isFilterInGridView' => true,
//                        'clearButton' => false
//                    ])
//                ],
//                [
//                    'label' => 'Завершение звонка (в АТС)',
//                    'attribute' => 'ats_finish_time',
//                    'content' => function ($model) {
//                        return (!empty($model->ats_finish_time) ? date('d.m.Y H:i:s', $model->ats_finish_time/1000) : '');
//                    },
//                    'filter' => PeriodPicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'ats_finish_time',
//                        'isFilterInGridView' => true,
//                        'clearButton' => false
//                    ])
//                ],

                [
                    'label' => 'Создан',
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (!empty($model->created_at) ? date('d.m.Y H:i:s', $model->created_at) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

//                [
//                    'label' => 'Начало разговора (в АТС)',
//                    'attribute' => 'ats_answer_time',
//                    'content' => function ($model) {
//                        return (!empty($model->ats_answer_time) ? date('d.m.Y H:i:s', $model->ats_answer_time/1000) : '');
//                    },
//                ],
//                [
//                    'label' => 'Начало разговора',
//                    'attribute' => 'answered_at',
//                    'content' => function ($model) {
//                        return (!empty($model->answered_at) ? date('d.m.Y H:i:s', $model->answered_at) : '');
//                    },
//                    'filter' => PeriodPicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'answered_at',
//                        'isFilterInGridView' => true,
//                        'clearButton' => false
//                    ])
//                ],

                [
                    'label' => 'Завершен',
                    'attribute' => 'finished_at',
                    'content' => function ($model) {
                        return (!empty($model->finished_at) ? date('d.m.Y H:i:s', $model->finished_at) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'finished_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
//        echo GridView::widget([
//            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
//            'layout'=>"{summary}\n{items}",
//            'options' => ['class' => 'grid-view table-responsive'],
//            'tableOptions' => [
//                'class' => 'table table-condensed table-bordered table-hover'
//            ],
//            'columns' => [
//                ['class' => 'yii\grid\SerialColumn'],
//
//                //'id',
//                [
//                    'attribute' => 'created_at',
//                    'content' => function($model) {
//                        return (!empty($model->created_at) ? date('d.m.Y H:i:s', $model->created_at) : '');
//                    }
//                ],
//                [
//                    'attribute' => 'callid',
//                    'label' => 'Код',
//                    'content' => function($model) {
//                        return $model->callid;
//                    }
//                ],
//                'is_opening',
//                'user_phone',
//                'user_id',
//                'mobile_ats_login',
////                'client_id',
////                'client_phone',
//                'subscriber_phone',
//                'subscriber_type',
//                'subscriber_id',
//                [
//                    'attribute' => 'call_from_operator',
//                    'label' => 'Тип звонка',
//                    'content' => function($model) {
//                        return $model->call_from_operator == 1 ? 'исходящий' : 'входящий';
//                    }
//                ],
//
//                //'outgoing_time:datetime',
//                [
//                    'attribute' => 'outgoing_time',
//                    'label' => 'опер.взял трубку для дозвона клиенту в',
//                    'content' => function($model) {
//                        return !empty($model->outgoing_time) ? date('d.m.Y H:i:s', $model->outgoing_time) : '';
//                    }
//                ],
//                //'incoming_time:datetime',
//                [
//                    'attribute' => 'incoming_time',
//                    'label' => 'у опера зазвонил телефон от звонка клиента в',
//                    'content' => function($model) {
//                        return !empty($model->incoming_time) ? date('d.m.Y H:i:s', $model->incoming_time) : '';
//                    }
//                ],
//                //'accepted_time:datetime',
//                [
//                    'attribute' => 'accepted_time',
//                    //'options' => ['style' => 'width: 150px;'],
//                    'label' => 'клиент взял трубку (исходящий звон) или опер взял трубку (входящий)',
//                    'content' => function($model) {
//                        return !empty($model->accepted_time) ? date('d.m.Y H:i:s', $model->accepted_time) : '';
//                    }
//                ],
//
//                //'completed_time:datetime',
//                [
//                    'attribute' => 'completed_time',
//                    'label' => 'звонок успешно завершен в',
//                    'content' => function($model) {
//                        return !empty($model->completed_time) ? date('d.m.Y H:i:s', $model->completed_time) : '';
//                    }
//                ],
//                //'canceled_time:datetime',
//                [
//                    'attribute' => 'canceled_time',
//                    'label' => 'звонок сброшен',
//                    'content' => function($model) {
//                        return !empty($model->canceled_time) ? date('d.m.Y H:i:s', $model->canceled_time) : '';
//                    }
//                ],
//
//                //['class' => 'yii\grid\ActionColumn'],
//            ],
//        ]);
        ?>
    </div>
</div>