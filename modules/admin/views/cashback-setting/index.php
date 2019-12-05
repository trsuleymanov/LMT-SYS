<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Кэш-беки';
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
                'order_penalty_percent',
                'hours_before_start_trip_for_penalty',
                [
                    'attribute' => 'with_commercial_trips',
                    'content' => function($model) {
                        return ($model->with_commercial_trips == true ? 'Да' : 'Нет');
                    }
                ],

                [
                    'attribute' => 'has_cashback_for_prepayment',
                    'content' => function($model) {
                        return ($model->has_cashback_for_prepayment == true ? 'Да' : 'Нет');
                    }
                ],
                [
                    'attribute' => 'has_cashback_for_nonprepayment',
                    'content' => function($model) {
                        return ($model->has_cashback_for_nonprepayment == true ? 'Да' : 'Нет');
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
