<?php

use app\helpers\table\PageSizeHelper;
use app\models\AdvertisingSource;
use app\models\DoTariff;
use app\widgets\periodPicker\PeriodPicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

$this->title = 'Отчет по рекламным источникам';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="advertising-source-report-index" class="box box-default">
    <div class="box-header scroller with-border">

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
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'advertising_source_id',
                    'content' => function ($model) {
                        return $model->advertisingSource != null ? $model->advertisingSource->name : '';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'advertising_source_id',
                        ['' => 'Все'] + ArrayHelper::map(AdvertisingSource::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'client_id',
                    'content' => function ($model) {
                        return $model->client != null ? '<a href="/admin/client/update?id='.$model->client_id.'">'.$model->client->name.'</a>' : '';
                    },
                    'filter' => false,
                ],

                [
                    'attribute' => 'operator_user_id',
                    'content' => function ($model) {
                        return $model->operatorUser != null ? $model->operatorUser->fullname : '';
                    },
                    'filter' => false,
                ],

                'phone',

                [
                    'label' => 'Время',
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y H:i', $model->created_at));
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    'template' => '{update} {delete}',
//                    'options' => ['style' => 'width: 50px;']
//                ],
            ],
        ]); ?>
    </div>
</div>
