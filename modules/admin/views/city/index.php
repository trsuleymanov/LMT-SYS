<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
use yii\helpers\Url;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Города';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>
<div id="city-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить город', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <!--
        ... class="pull-left"
        -->
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'name',
                [
                    'attribute' => 'extended_external_use',
                    'content' => function ($model) {
                        return $model->extended_external_use == 1 ? 'да' : 'нет';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'extended_external_use',
                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                        ['class' => "form-control"]
                    )
                ],
                'center_lat',
                'center_long',
                'map_scale',
                'search_scale',
                'point_focusing_scale',
                'all_points_show_scale',
                [
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return date('d.m.Y', $model->created_at);
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'created_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ]),
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
                        return (!empty($model->updated_at) ? date('d.m.Y', $model->updated_at) : '');
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
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                Url::to(['/admin/city/ajax-delete', 'id' => $model->id]),
                                [
                                    'aria-label' => 'Удалить',
                                    'class' => "delete-city",
//                                    'data-pjax' => "0",
//                                    'data-confirm' => "Вы уверены, что хотите удалить этот элемент?",
//                                    'data-method' => "post"
                                ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
