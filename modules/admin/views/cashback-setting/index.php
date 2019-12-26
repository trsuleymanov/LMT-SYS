<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Кэш-бэки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="cashback-setting-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить систему КБ', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
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
                    'attribute' => 'start_date',
                    'content' => function($model) {
                        return date('d.m.Y', $model->start_date);
                    }
                ],
                'order_accrual_percent',
                // 'order_penalty_percent',
                // 'hours_before_start_trip_for_penalty',
                [
                    'attribute' => 'with_commercial_trips',
                    'content' => function($model) {
                        return ($model->with_commercial_trips == true ? 'Да' : 'Нет');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'with_commercial_trips',
                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                        ['class' => "form-control"]
                    )
                ],
//                [
//                    'attribute' => 'has_cashback_for_prepayment',
//                    'content' => function($model) {
//                        return ($model->has_cashback_for_prepayment == true ? 'Да' : 'Нет');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'has_cashback_for_prepayment',
//                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'has_cashback_for_nonprepayment',
//                    'content' => function($model) {
//                        return ($model->has_cashback_for_nonprepayment == true ? 'Да' : 'Нет');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'has_cashback_for_nonprepayment',
//                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
//                        ['class' => "form-control"]
//                    )
//                ],

                [
                    'attribute' => 'cashback_type',
                    'content' => function($model) {
                        //return ($model->cashback_type == '' ? 'Да' : 'Нет');
                        if($model->cashback_type == 'with_prepayment') {
                            return 'КБ с предоплатой';
                        }else {
                            return 'КБ без предоплаты';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'has_cashback_for_nonprepayment',
                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'red_penalty_max_time',
                    'content' => function($model) {
                        return $model->red_penalty_max_time;
                    }
                ],
                [
                    'attribute' => 'order_red_penalty_percent',
                    'content' => function($model) {
                        return $model->order_red_penalty_percent;
                    }
                ],

                [
                    'attribute' => 'yellow_penalty_max_time',
                    'content' => function($model) {
                        return $model->yellow_penalty_max_time;
                    }
                ],
                [
                    'attribute' => 'order_yellow_penalty_percent',
                    'content' => function($model) {
                        return $model->order_yellow_penalty_percent;
                    }
                ],

                [
                    'attribute' => 'max_time_confirm_diff',
                    'content' => function($model) {
                        return $model->max_time_confirm_diff;
                    }
                ],
                [
                    'attribute' => 'max_time_confirm_delta',
                    'content' => function($model) {
                        return $model->max_time_confirm_delta;
                    }
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'update' => function ($url, $model)
                        {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                Url::to(['/admin/cashback-setting/update', 'id' => $model->id]),
                                [
                                    'title' => 'Редактировать',
                                    'aria-label' => 'Редактировать'
                                ]
                            );
                        },
//                        'delete' => function ($url, $model)
//                        {
//                            if($model->id == 1) {
//                                return '';
//                            }else {
//                                return Html::a(
//                                    '<span class="glyphicon glyphicon-trash"></span>',
//                                    Url::to(['/admin/cashback-setting/delete', 'id' => $model->id]),
//                                    [
//                                        'title' => 'Удалить',
//                                        'aria-label' => 'Удалить',
//                                        'data-pjax' => "0",
//                                        'data-confirm' => "Вы уверены, что хотите удалить тариф?",
//                                        'data-method' => "post",
//                                    ]);
//                            }
//                        },
                    ],
                ],
            ],
        ]); ?>

    </div>

</div>
