<?php

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
use app\helpers\table\PageSizeHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\widgets\periodPicker\PeriodPicker;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);

$this->title = 'Аномальные заказы';
$this->params['breadcrumbs'][] = $this->title;


$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
?>
<div id="abnormal-order-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить заказ', ['create'], ['class' => 'btn btn-success']) ?>
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
        <?php Pjax::begin([
            'id' => 'abnormal-order-grid'
        ]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'status_id',
                    'content' => function ($model) {
                        if($model->status_id > 0) {
                            return $model->status->name;
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status_id',
                        ['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'cancellation_reason_id',
                    'content' => function ($model) {
                        return ($model->cancellation_reason_id > 0 ? $model->cancellationReason->name : '');
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Создан',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y H:i', $model->created_at));
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'created_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'updated_at',
                    'label' => 'Изменен',
                    'content' => function ($model) {
                        return (empty($model->updated_at) ? '' : date('d.m.Y H:i', $model->updated_at));
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'updated_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'date',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'trip_id',
                    'content' => function($model) {
                        return $model->trip != null ? $model->trip->name : '';
                    },
                ],
//                [
//                    'attribute' => 'is_trip_id',
//                    'content' => function($model) {
//                        return $model->trip != null ? $model->trip->name : '';
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'point_id_from',
//                        ['' => 'Все', 0 => 'Существует', ''],
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'client_id',
                    'content' => function ($model) {
                        if($model->client_id > 0) {
                            return $model->client->name.' '.$model->client->mobile_phone;
                        }
                    },
                ],
                [
                    'attribute' => 'fact_trip_transport_id',
                    'label' => 'Т/с',
                    'content' => function($model) {
                        if($model->fact_trip_transport_id > 0) {
                            $fact_trip_transport = $model->factTripTransport;
                            if($fact_trip_transport == null) {
                                return 'отсутствует trip_transport[id='.$model->fact_trip_transport_id.']';
                            }else {
                                $transport = $fact_trip_transport->transport;
                                return $transport->name3;
                            }
                        }else {
                            return '';
                        }
                    }
                ],
//                [
//                    'attribute' => 'point_id_from',
//                    'content' => function($model) {
//                        if($model->point_id_from > 0) {
//                            $point = $model->pointFrom;
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'point_id_from',
//                        ['' => 'Все'] + $point_list,
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'point_id_to',
//                    'content' => function($model) {
//                        if($model->point_id_to > 0) {
//                            $point = $model->pointTo;
//                            if($point == null) {
//                                return '';
//                            }
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'point_id_to',
//                        ['' => 'Все'] + $point_list,
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'yandex_point_from_name',
                    'label' => 'Откуда',
                    'content' => function($model) {
                        return $model->yandex_point_from_name;
                    },
                    'filter' => Html::activeTextInput(
                        $searchModel,
                        'yandex_point_from_name',
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'yandex_point_to_name',
                    'label' => 'Куда',
                    'content' => function($model) {
                        return $model->yandex_point_to_name;
                    },
                    'filter' => Html::activeTextInput(
                        $searchModel,
                        'yandex_point_to_name',
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'prize_trip_count',
                    'label' => 'Пр.',
                    'content' => function($model) {
                        return intval($model->prize_trip_count);
                    },
                ],
                [
                    'attribute' => 'places_count',
                    'label' => 'М',
                    'content' => function($model) {
                        return !empty($model->places_count) ? $model->places_count : 0;
                    },
                ],
                [
                    'attribute' => 'student_count',
                    'label' => 'С',
                    'content' => function($model) {
                        return !empty($model->student_count) ? $model->student_count : 0;
                    },
                ],
                [
                    'attribute' => 'child_count',
                    'label' => 'Д',
                    'content' => function($model) {
                        return !empty($model->child_count) ? $model->child_count : 0;
                    },
                ],
                [
                    'attribute' => 'bag_count',
                    'label' => 'Багаж',
                    'content' => function($model) {
                        return intval($model->bag_count).', '.intval($model->suitcase_count).', '.intval($model->oversized_count);
                    }
                ],


//                [
//                    'attribute' => 'baggage',
//                    'content' => function($model) {
//                        return $model->baggageName;
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'baggage',
//                        ['' => 'Все', ] + Order::$baggageList,
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'is_not_places',
//                    'content' => function($model) {
//                        return ($model->is_not_places == 1 ? 'Без места' : '');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'is_not_places',
//                        ['' => 'Все', '1' => 'Без места', '0' => 'С местом'],
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'time_confirm',
                    'label' => 'ВРПТ',
                    'content' => function ($model) {
                        return (empty($model->time_confirm) ? '' : date('d.m.Y H:i', $model->time_confirm));
                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'time_confirm',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_confirm',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                'comment',
                //'contacts',
                [
                    'attribute' => 'time_sat',
                    'label' => 'Посадка',
                    'content' => function ($model) {
                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'time_sat',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_sat',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-remove-sign text-danger"></span>',
                                Url::to(['/admin/order/ajax-delete', 'id' => $model->id]),
                                [
                                    'aria-label' => 'Удалить',
                                    'class' => "remove-order"
                                ]);
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php

?>