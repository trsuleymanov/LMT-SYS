<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="tariff-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить тариф', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'update' => function ($url, $model)
                        {
                            if($model->id == 1) {
                                return '';
                            }else {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/tariff/update', 'id' => $model->id]),
                                    [
                                        'title' => 'Редактировать',
                                        'aria-label' => 'Редактировать'
                                    ]
                                );
                            }
                        },
                        'delete' => function ($url, $model)
                        {
                            if($model->id == 1) {
                                return '';
                            }else {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/tariff/delete', 'id' => $model->id]),
                                    [
                                        'title' => 'Удалить',
                                        'aria-label' => 'Удалить',
                                        'data-pjax' => "0",
                                        'data-confirm' => "Вы уверены, что хотите удалить тариф?",
                                        'data-method' => "post",
                                    ]);
                            }
                        },
                    ],
                ],

                //'id',
                [
                    'attribute' => 'commercial',
                    'label' => 'Спец. тариф',
                    'content' => function($model) {
                        return $model->commercial == true ? 'да' : '-';
                    }
                ],
                [
                    'attribute' => 'start_date',
                    'content' => function($model) {
                        return date('d.m.Y', $model->start_date);
                    }
                ],

//                'common_price',
//                'student_price',
//                'baby_price',
//                'aero_price',
//                'parcel_price',
//                'loyal_price',

                'unprepayment_common_price',
                'unprepayment_student_price',
                'unprepayment_baby_price',
                'unprepayment_aero_price',
                'unprepayment_parcel_price',
                'unprepayment_loyal_price',
                'unprepayment_reservation_cost',

                'prepayment_common_price',
                'prepayment_student_price',
                'prepayment_baby_price',
                'prepayment_aero_price',
                'prepayment_parcel_price',
                'prepayment_loyal_price',
                'prepayment_reservation_cost',

                'superprepayment_common_price',
                'superprepayment_student_price',
                'superprepayment_baby_price',
                'superprepayment_aero_price',
                'superprepayment_parcel_price',
                'superprepayment_loyal_price',
                'superprepayment_reservation_cost',

            ],
        ]); ?>
    </div>
</div>
