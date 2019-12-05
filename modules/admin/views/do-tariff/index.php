<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\widgets\periodPicker\PeriodPicker;


$this->title = 'Признаки формирования цены';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div class="do-tariff-page" class="box box-default">
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить признак', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div></div>

    <div class="box-body box-table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'description',
                    //'tariff_type',
                    //'code',
                    [
                        'attribute' => 'tariff_type',
                        'content' => function ($model) {
                            if($model->tariff_type == 'order') {
                                return 'для заказа';
                            }elseif($model->tariff_type == 'client') {
                                return 'для клиента';
                            }
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'tariff_type',
                            ['' => 'Все', 'order' => 'для заказа', 'client' => 'для клиента'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'use_client_do_tariff',
                        'content' => function ($model) {
                            return ($model->use_client_do_tariff == true ? 'да' : 'нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'use_client_do_tariff',
                            ['' => 'Все', 0 => 'нет', 'да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'place_price_formula',
                        'content' => function ($model) {
                            return !empty($model->place_price_formula) ? $model->place_price_formula : '';
                        },
                    ],
                    [
                        'attribute' => 'use_fix_price',
                        'content' => function ($model) {
                            return ($model->use_fix_price == true ? 'да' : 'нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'use_fix_price',
                            ['' => 'Все', 0 => 'нет', 'да'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'order_price_formula',
                        'content' => function ($model) {
                            return !empty($model->order_price_formula) ? $model->order_price_formula : '';
                        },
                    ],
                    [
                        'attribute' => 'order_comment',
                        'content' => function ($model) {
                            return !empty($model->order_comment) ? $model->order_comment : '';
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'content' => function ($model) {
                            return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
                        },
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
                        'filter' => PeriodPicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'updated_at',
                            'isFilterInGridView' => true,
                            'clearButton' => false
                        ])
                    ],

                    //['class' => 'yii\grid\ActionColumn'],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'options' => ['style' => 'width: 25px;']
                    ],
                ],
            ]); ?>
        </div>
    </div>
