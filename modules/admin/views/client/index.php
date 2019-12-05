<?php

use app\models\DoTariff;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\Point;
use kartik\field\FieldRange;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

?>
<div id="client-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить клиента', ['create'], ['class' => 'btn btn-sm btn-success']) ?>
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
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout'=>"{summary}\n{items}",
            //'layout'=>"{summary}\n{pager}\n{items}\n{pager}",
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                [
                    'attribute' => 'do_tariff_id',
                    'content' => function ($model) {
                        return $model->doTariff != null ? $model->doTariff->description : '';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'do_tariff_id',
                        ['' => 'Все'] + ArrayHelper::map(DoTariff::find()->where(['tariff_type' => 'client'])->all(), 'id', 'description'),
                        ['class' => "form-control"]
                    )
                ],
                'mobile_phone',
                'home_phone',
                'alt_phone',
//                [
//                    'attribute' => 'last_point_from',
//                    'content' => function($model) {
//                        if($model->last_point_from > 0) {
//                            $point = $model->lastPointFrom;
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'last_point_from',
//                        ['' => 'Все'] + ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'last_point_to',
//                    'content' => function($model) {
//                        if($model->last_point_to > 0) {
//                            $point = $model->lastPointTo;
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'last_point_to',
//                        ['' => 'Все'] + ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'rating',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'rating_from',
                        'attribute2' => 'rating_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'sended_orders_places_count',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'sended_orders_places_count_from',
                        'attribute2' => 'sended_orders_places_count_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'sended_prize_trip_count',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'sended_prize_trip_count_from',
                        'attribute2' => 'sended_prize_trip_count_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'penalty',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'penalty_from',
                        'attribute2' => 'penalty_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
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
                    'content' => function ($model) {
                        return (empty($model->updated_at) ? '' : date('d.m.Y', $model->updated_at));
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>

