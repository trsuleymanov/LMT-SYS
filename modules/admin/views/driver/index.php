<?php

use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\Transport;
use app\helpers\table\PageSizeHelper;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);

$this->title = 'Водители';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$arTransport = ArrayHelper::map(Transport::find()->where(['active' => 1])->orderBy(['CONVERT(car_reg,SIGNED)' => SORT_ASC])->all(), 'id', 'name5');
?>
<div id="driver-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить водителя', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'active',
                    'label' => 'Акт.',
                    'content' => function ($model) {

                        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                            return '<input class="driver-active" driver-id=' . $model->id . ' ' . ($model->active == true ? 'checked' : '') . ' type="checkbox">';
                        }else {
                            return ($model->active == 1 ? 'да' : 'нет');
                        }
                    },
                ],
                [
                    'attribute' => 'accountability',
                    'content' => function ($model) {

                        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                            return '<input class="driver-accountability" driver-id=' . $model->id . ' ' . ($model->accountability == true ? 'checked' : '') . ' type="checkbox">';
                        }else {
                            return ($model->accountability == 1 ? 'да' : 'нет');
                        }
                    },
                ],
                'fio',
                'mobile_phone',
                [
                    'attribute' => 'device_code',
                    'label' => 'Код устройства',
                    'content' => function($model) {
                        return $model->device_code;
                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'primary_transport_id',
//                        ['' => 'Все'] + $arTransport,
//                        ['class' => "form-control"]
//                    )
                ],
                'home_phone',
                [
                    'attribute' => 'primary_transport_id',
                    'label' => 'Основное т/с',
                    'content' => function($model) {
                        return $model->primaryTransport->name5;
                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'primary_transport_id',
//                        ['' => 'Все'] + $arTransport,
//                        ['class' => "form-control"]
//                    )
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'primary_transport_id',
                        'name' => 'primary_transport_id',
                        'initValueText' => ($searchModel->primary_transport_id > 0 && $searchModel->primaryTransport != null ? $searchModel->primaryTransport->name5 : ''),
                        'options' => [
                            'placeholder' => '',
                        ],
                        'ajax' => [
                            'url' => '/admin/transport/ajax-get-transports?field_name=name5',
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
                    'attribute' => 'secondary_transport_id',
                    'label' => 'Дополнительное т/с',
                    'content' => function($model) {
                        return $model->secondaryTransport != null ? $model->secondaryTransport->name5 : '';
                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'secondary_transport_id',
//                        ['' => 'Все'] + $arTransport,
//                        ['class' => "form-control"]
//                    )
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'secondary_transport_id',
                        'name' => 'secondary_transport_id',
                        'initValueText' => ($searchModel->secondary_transport_id > 0 && $searchModel->secondaryTransport != null ? $searchModel->secondaryTransport->name5 : ''),
                        'options' => [
                            'placeholder' => '',
                        ],
                        'ajax' => [
                            'url' => '/admin/transport/ajax-get-transports?field_name=name5',
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
