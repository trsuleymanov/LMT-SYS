<?php

use app\models\DispatcherAccounting;
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

$this->title = 'Электронные заявки';
$this->params['breadcrumbs'][] = $this->title;


$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

?>
<div id="electronic-request-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">

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
        <?php
        $aDirections = [
            1 => 'АК',
            2 => 'КА'
        ];
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'external_created_at',
                    'content' => function ($model) {
                        return (empty($model->external_created_at) ? '' : date('d.m.Y', $model->external_created_at));
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'external_created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'direction_id',
                    'content' => function ($model) use($aDirections) {
                        return (!empty($model->direction_id) && isset($aDirections[$model->direction_id])  ? $aDirections[$model->direction_id] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'direction_id',
                        ['' => 'Все'] + $aDirections,
                        ['class' => "form-control"]
                    )
                ],
                [
                    'label' => 'Телефон',
                    'attribute' => 'client_name_phone',
                    'content' => function ($model) {
                        return ($model->client != null ? $model->client->mobile_phone : '');
                    },
                ],
                [
                    'label' => 'Время создания',
                    'attribute' => 'external_created_at',
                    'content' => function ($model) {
                        return (!empty($model->external_created_at) ? date('d.m.Y H:i', $model->external_created_at) : '');
                    },
                ],
                [
                    'label' => 'Обработанность',
                    'attribute' => 'handling_time',
                    'content' => function ($model) {
                        $text = '-';
                        $dispetcher_accounting = DispatcherAccounting::find()
                            ->where(['operation_type' => 'handling_client_server_request'])
                            ->where(['order_id' => $model->id])
                            ->one();
                        if($dispetcher_accounting != null) {
                            $text = date('d.m.Y H:i', $dispetcher_accounting->created_at);
                            if($dispetcher_accounting->user != null) {
                                $text .= '<br />оператор '.$dispetcher_accounting->user->fullname;
                            }
                        }

                        if($model->status_id > 0) {
                            $text .= '<br />создан заказ '.$model->id;
                        }

                        return $text;
                    }
                ],
//                [
//                    'label' => 'Результат обработки',
//                    'attribute' => 'status_id',
//                    'content' => function ($model) {
//                        if($model->status_id > 0) {
//                            return 'создан заказ';
//                        }else {
//                            return '-';
//                        }
//                    }
//                ],
            ],
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
