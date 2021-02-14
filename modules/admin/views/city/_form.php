<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\models\Point;
use app\models\City;
use kartik\date\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=5c7acdc8-48c9-43d9-9f44-2e6b9e178101', ['depends' => 'app\assets\AdminAsset']);
?>

<?php $form = ActiveForm::begin([
    'id' => 'city-form',
    'options' => [
        'city-id' => $model->id
    ]
]); ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Основная информация
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">

        <br />
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'extended_external_use')->checkbox() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'center_lat')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2 form-group form-group-sm">
                <?= $form->field($model, 'center_long')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 form-group form-group-sm">
                <?= $form->field($model, 'map_scale')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3 form-group form-group-sm">
                <?= $form->field($model, 'search_scale')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3 form-group form-group-sm">
                <?= $form->field($model, 'point_focusing_scale')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3 form-group form-group-sm">
                <?= $form->field($model, 'all_points_show_scale')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить и выйти', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


<?php
if(!$model->isNewRecord)
{ ?>
    <div id="yandex-point-list" class="box box-solid">

        <div class="box-header scroller with-border">
            <h3 class="box-title">
                <i class="fa fa-address-book-o"></i>
                Яндекс точки
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>


        <div class="box-body box-table">

            <?= Html::a('<i class="fa fa-plus"></i> Добавить опорную точку', Url::to(['/admin/yandex-point/ajax-create', 'city_id' => $model->id]), ['id'=>'add-yandex-point', 'class' => 'btn btn-success']) ?>
            <br /><br />

            <?php Pjax::begin([
                'id' => 'yandex-points-grid'
            ]) ?>

            <?= GridView::widget([
                'dataProvider' => $yandexPointDataProvider,
                'filterModel' => $yandexPointSearchModel,
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
                            'update' => function ($url, $model) {
//                                return Html::a(
//                                    '<span class="glyphicon glyphicon-pencil"></span>',
//                                    Url::to(['/admin/yandex-point/ajax-update', 'id' => $model->id]),
//                                    ['aria-label' => 'Редактировать', 'class' => "edit-yandex-point"]
//                                );
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    '#',
                                    [
                                        'aria-label' => 'Редактировать',
                                        'class' => "edit-yandex-point",
                                        'yandex-point-id' => $model->id
                                    ]
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/yandex-point/ajax-delete', 'id' => $model->id]),
                                    [
                                        'aria-label' => 'Удалить',
                                        'class' => "delete-yandex-point"
                                    ]
                                );
                            },
                        ],
                    ],

                    'id',
                    [
                        'attribute' => 'external_use',
                        'content' => function($model) {
                            return $model->external_use == true ? 'да' : 'нет';
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'external_use',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                    'name',
                    [
                        'attribute' => 'popular_departure_point',
                        'content' => function($model) {
                            return $model->popular_departure_point == true ? 'да' : 'нет';
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'popular_departure_point',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'popular_arrival_point',
                        'content' => function($model) {
                            return $model->popular_arrival_point == true ? 'да' : 'нет';
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'popular_arrival_point',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'city_id',
                        'content' => function($model) {
                            return $model->city->name;
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'city_id',
                            ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                            ['class' => "form-control"]
                        )
                    ],
                    'lat',
                    'long',
                    [
                        'attribute' => 'point_of_arrival',
                        'content' => function($model) {
                            return $model->point_of_arrival == 1 ? 'Да' : '';
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'point_of_arrival',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'critical_point',
                        'content' => function($model) {
                            return $model->critical_point == 1 ? 'Да' : '';
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'critical_point',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'alias',
                        'label' => 'Алиас'
                    ],
                    [
                        'attribute' => 'point_from_standart_price_diff',
                    ],
                    [
                        'attribute' => 'point_from_commercial_price_diff',
                    ],
                    [
                        'attribute' => 'point_to_standart_price_diff',
                    ],
                    [
                        'attribute' => 'point_to_commercial_price_diff',
                    ],
                    [
                        'attribute' => 'time_to_get_together_short',
                        'content' => function ($model) {
                            return $model->time_to_get_together_short;
                        }
                    ],
                    [
                        'attribute' => 'time_to_get_together_long',
                        'content' => function ($model) {
                            return $model->time_to_get_together_short;
                        }
                    ],
//                    [
//                        'attribute' => 'created_at',
//                        'content' => function ($model) {
//                            return (!empty($model->created_at) ? date('d.m.Y', $model->created_at) : '');
//                        },
//                        'filter' => DatePicker::widget([
//                            'model' => $yandexPointSearchModel,
//                            'attribute' => 'created_at',
//                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'dd.mm.yyyy',
//                            ]
//                        ]),
//                    ],
//                    [
//                        'attribute' => 'creator_id',
//                        'content' => function ($model) {
//                            if(!empty($model->creator_id)) {
//                                return $model->creator->fio;
//                            }else {
//                                return '';
//                            }
//                        }
//                    ],
//                    [
//                        'attribute' => 'updated_at',
//                        'content' => function ($model) {
//                            return (!empty($model->updated_at) ? date('d.m.Y', $model->updated_at) : '');
//                        },
//                        'filter' => DatePicker::widget([
//                            'model' => $yandexPointSearchModel,
//                            'attribute' => 'updated_at',
//                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'dd.mm.yyyy',
//                            ]
//                        ])
//                    ],
//                    [
//                        'attribute' => 'updater_id',
//                        'content' => function ($model) {
//                            if(!empty($model->updater_id)) {
//                                return $model->updater->fio;
//                            }else {
//                                return '';
//                            }
//                        }
//                    ],


                    [
                        'attribute' => 'super_tariff_used',
                        'content' => function ($model) {
                            return ($model->super_tariff_used == true ? 'Да' : '');
                        },
                        'filter' => Html::activeDropDownList(
                            $yandexPointSearchModel,
                            'super_tariff_used',
                            ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                            ['class' => "form-control"]
                        )
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

<?php } ?>


<?php
if(!$model->isNewRecord)
{ ?>
    <div id="streets-list" class="box box-solid">

        <div class="box-header scroller with-border">
            <h3 class="box-title">
                <i class="fa fa-address-book-o"></i>
                Улицы
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>


        <div class="box-body box-table">

            <?= Html::a('<i class="fa fa-plus"></i> Добавить улицу', Url::to(['/admin/street/ajax-create', 'city_id' => $model->id]), ['id'=>'add-street', 'class' => 'btn btn-success']) ?>
            <br /><br />

            <?php Pjax::begin([
                'id' => 'streets-grid'
            ]) ?>

            <?= GridView::widget([
                'dataProvider' => $streetDataProvider,
                'filterModel' => $streetSearchModel,
                //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => [
                    'class' => 'table table-condensed table-bordered table-hover'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    [
                        'attribute' => 'city_id',
                        'content' => function($model) {
                            return $model->city->name;
                        },
                        'filter' => Html::activeDropDownList(
                            $streetSearchModel,
                            'city_id',
                            ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                            ['class' => "form-control"]
                        )
                    ],
                    'name',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'options' => ['style' => 'width: 50px;'],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/street/ajax-update', 'id' => $model->id]),
                                    ['aria-label' => 'Редактировать', 'class' => "edit-street"]
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/street/ajax-delete', 'id' => $model->id]),
                                    [
                                        'aria-label' => 'Удалить',
                                        'class' => "delete-street"
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

<?php } ?>

<?php
if(!$model->isNewRecord)
{ ?>
    <div id="points-list" class="box box-solid">

        <div class="box-header scroller with-border">
            <h3 class="box-title">
                <i class="fa fa-address-book-o"></i>
                Ориентиры
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>


        <div class="box-body box-table">

            <?= Html::a('<i class="fa fa-plus"></i> Добавить ориентир', Url::to(['/admin/point/ajax-create', 'city_id' => $model->id]), ['id'=>'add-point', 'class' => 'btn btn-success']) ?>
            <br /><br />

            <?php Pjax::begin([
                'id' => 'points-grid'
            ]) ?>

            <?= GridView::widget([
                'dataProvider' => $pointDataProvider,
                'filterModel' => $pointSearchModel,
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
                        'attribute' => 'city_id',
                        'content' => function($model) {
                            return $model->city->name;
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'city_id',
                            ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'alias',
                        'content' => function($model) {
                            return (empty($model->alias) ? '' : $model->alias);
                        },
                    ],
                    [
                        'attribute' => 'point_of_arrival',
                        'content' => function($model) {
                            return ($model->point_of_arrival == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'point_of_arrival',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],

                    [
                        'attribute' => 'critical_point',
                        'content' => function($model) {
                            return ($model->critical_point == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'critical_point',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'created_at',
                        'content' => function ($model) {
                            return date('d.m.Y', $model->created_at);
                        },
                        'filter' => DatePicker::widget([
                            'model' => $pointSearchModel,
                            'attribute' => 'created_at',
                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ]
                        ]),
                    ],
                    [
                        'attribute' => 'updated_at',
                        'content' => function ($model) {
                            return (!empty($model->updated_at) ? date('d.m.Y', $model->updated_at) : '');
                        },
                        'filter' => DatePicker::widget([
                            'model' => $pointSearchModel,
                            'attribute' => 'updated_at',
                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'active',
                        'content' => function($model) {
                            return ($model->active == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'active',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'options' => ['style' => 'width: 50px;'],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/point/ajax-update', 'id' => $model->id]),
                                    ['aria-label' => 'Редактировать', 'class' => "edit-point"]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/point/ajax-delete', 'id' => $model->id]),
                                    [
                                        'aria-label' => 'Удалить',
                                        'class' => "delete-point"
                                    ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

<?php } ?>
