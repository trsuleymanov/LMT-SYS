<?php
use app\models\Driver;
use app\models\Storage;
use app\models\StorageOperationType;
use app\models\Transport;
use app\models\User;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\Helper;
use app\models\Direction;
use app\models\Trip;
use app\models\ScheduleTrip;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use kartik\date\DatePicker;
use yii\web\JsExpression;
use app\widgets\SelectWidget;
use app\widgets\periodPicker\PeriodPicker;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;

//$this->registerJsFile('js/site/create-order-modal.js', ['depends'=>'app\assets\AppAsset']);
//$this->registerJsFile('js/site/index.js', ['depends'=>'app\assets\AppAsset']);
//$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['depends' => 'app\assets\AdminAsset']);

$user = Yii::$app->user->identity;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="operation-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">

        </div>

        <div class="pull-left">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => [
                    'class' => 'pagination pagination-sm'
                ]
            ]); ?>
        </div>
        <?= (new PageSizeHelper([20, 50, 100, 200, 500]))->getButtons() ?>
    </div>
    <div></div>

    <div class="box-body box-table">
        <?php

        $columns = [

            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'storage_id',
                'label' => 'Склад',
                'content' => function($model) {
                    $storage = $model->storageDetail != null ?  $model->storageDetail->storage : null;
                    return $storage != null ? $storage->name : '';
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'storage_id',
                    ['' => 'Все'] + ArrayHelper::map(Storage::find()->all(), 'id', 'name'),
                    ['class' => "form-control"]
                )
            ],
            [
                //'attribute' => 'storage_detail_id',
                'attribute' => 'nomenclature_detail_name',
                'label' => 'Запчасть',
                'content' => function($model) {
                    $nomenclature_detail = $model->storageDetail->nomenclatureDetail;
                    return
                        ($nomenclature_detail != null && $nomenclature_detail->detailName != null ? $nomenclature_detail->detailName->name : '')
                        . ' ('.$nomenclature_detail->transportModel->sh_name.')'
                        . ' - '.mb_strtolower($nomenclature_detail->installationPlace, 'UTF-8')
                        . ' / '.mb_strtolower($nomenclature_detail->installationSide, 'UTF-8');
                },
            ],
            [
                'attribute' => 'detail_state_id',
                'label' => 'Состояние',
                'content' => function($model) {
                    return mb_strlen($model->storageDetail->transportDetailState->name, 'UTF-8') > 10 ? mb_substr($model->storageDetail->transportDetailState->name, 0, 10, 'UTF-8').'.' : $model->storageDetail->transportDetailState->name;
                },
            ],
            [
                'attribute' => 'detail_origin_id',
                'label' => 'Происх.',
                'content' => function($model) {
                    return mb_strlen($model->storageDetail->transportDetailOrigin->name, 'UTF-8') > 7 ? mb_substr($model->storageDetail->transportDetailOrigin->name, 0, 7, 'UTF-8').'.' : $model->storageDetail->transportDetailOrigin->name;
                },
            ],

            [
                'attribute' => 'count',
                'label' => 'Кол-во',
                'content' => function($model) {
                    return $model->count.' '.$model->storageDetail->nomenclatureDetail->detailMeasurementValue->name;
                },
            ],

            [
                'attribute' => 'transport_id',
                'headerOptions' => [
                    'style' => 'width: 140px;',
                ],
                'content' => function($model) {
//                    $transport_name = ($model->transport != null ? $model->transport->name2 : '');
//                    if($model->without_transport == 1 || $transport_name == 'ТТ (000)') {
//                        return 'без участия т/с';
//                    }else {
//                        return $transport_name;
//                    }

                    return ($model->transport != null ? $model->transport->name2 : '');
                },
                'filter' => SelectWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'transport_id',
                    'value' => $searchModel->transport != null ? $searchModel->transport->name2 : '',
                    'options' => [
                        'placeholder' => 'Введите название...',
                    ],
                    'ajax' => [
                        'url' => '/transport/ajax-get-active-transports',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search
                            };
                        }'),
                    ],
                ])
            ],

            [
                'attribute' => 'driver_id',
                'content' => function($model) {
                    $transport_name = ($model->transport != null ? $model->transport->name2 : '');
                    if($transport_name == 'ТТ (000)') {
                        return '';
                    }else {
                        return $model->driver != null ? $model->driver->fio : '';
                    }
                },
                'filter' => SelectWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'driver_id',
                    'value' => $searchModel->driver != null ? $searchModel->driver->fio : '',
                    'options' => [
                        'placeholder' => 'Введите название...',
                    ],
                    'ajax' => [
                        'url' => '/driver/ajax-get-active-drivers',
                        'data' => new JsExpression('function(params) {
                            return {
                                search: params.search
                            };
                        }'),
                    ],
                ])
            ],

            [
                'attribute' => 'date',
                'label' => 'Дата',
                'content' => function($model) {
                    return ($model->date > 0 ? date("d.m.Y", $model->date) : '');
                },
    //                'filter' => DatePicker::widget([
    //                    'model' => $searchModel,
    //                    'attribute' => 'created_at',
    //                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
    //                    'pluginOptions' => [
    //                        'autoclose' => true,
    //                        'format' => 'dd.mm.yyyy',
    //                    ]
    //                ])
                'filter' => PeriodPicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'isFilterInGridView' => true,
                    'clearButton' => false
                ])
            ],

            [
                'attribute' => 'creator_id',
                'header' => 'Провел <br>Операцию',
                'content' => function($model) {
                    return $model->creator->lastname.'<br />'.$model->creator->firstname;
                },
    //            'filter' => Html::activeDropDownList(
    //                $searchModel,
    //                'creator_id',
    //                ['' => 'Все'] + ArrayHelper::map(User::find()->all(), 'id', 'fullname'),
    //                ['class' => "form-control"]
    //            ),

            ],

            [
                'attribute' => 'operation_type_id',
                'header' => 'Тип<br />операции',
                'content' => function($model) {
                    return $model->storageOperationType->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'operation_type_id',
                    ['' => 'Все'] + ArrayHelper::map(StorageOperationType::find()->all(), 'id', 'name'),
                    ['class' => "form-control"]
                )
            ],
            [
                'attribute' => 'comment',
                'header' => 'Ком-й',
                'content' => function($model) {
                    return $model->comment;
                },
            ],
        ];

        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
            $columns[] = [
                'attribute' => 'xz',
                'label' => '',
                'content' => function($model) {
                    return date("d.m.Y", $model->created_at) != date("d.m.Y", $model->date) ? date("d.m.Y", $model->created_at) : '';
                },
            ];
        }

        if(in_array(Yii::$app->session->get('role_alias'), ['root'])) {
            $columns[] = [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => ['style' => 'width: 40px;'],
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            '#',
                            [
                                'aria-label' => 'Редактировать',
                                'class' => 'storage-operation-update',
                            ]
                        );
                    },
                    'delete' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            Url::to(['/storage/operation/delete', 'id' => $model->id]),
                            [
                                'aria-label' => 'Удалить',
                                'data-method' => "post",
                                'data-pjax'  => 0,
                                'data-confirm' => "Вы уверены, что хотите удалить этот элемент?",
                            ]);
                    },
                ],
            ];
        }


        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout'=>"{summary}\n{items}",
            'layout'=>"{items}",
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => $columns,
        ]); ?>
        </div>
    </div>
</div>

