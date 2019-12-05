<?php

use app\models\CallCase;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;

$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>
<div id="call-case-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left"></div>

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
                    'attribute' => 'call_count',
                    'content' => function ($model) {
                        return (!empty($model->call_count) ? '<a href="#" class="toggle-calls" is-open="false">'.$model->call_count.'</a>' : 0);
                    },
                ],

                [
                    'attribute' => 'case_type',
                    'content' => function ($model) {
                        return (!empty($model->case_type) ? CallCase::getTypes(true)[$model->case_type] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'case_type',
                        ['' => 'Все'] + CallCase::getTypes(),
                        ['class' => "form-control"]
                    )
                ],

                [
                    'attribute' => 'order_id',
                    'content' => function ($model) {
                        return (!empty($model->order_id) ? $model->order_id : '');
                    },
                ],

                [
                    'attribute' => 'open_time',
                    'content' => function ($model) {
                        return (!empty($model->open_time) ? date('d.m.Y H:i:s', $model->open_time) : '');
                    },
                ],

                //'status',
                [
                    'attribute' => 'status',
                    'content' => function ($model) {
                        return (!empty($model->status) ? CallCase::getStatuses(true)[$model->status] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status',
                        ['' => 'Все'] + CallCase::getStatuses(),
                        ['class' => "form-control"]
                    )
                ],


                [
                    'attribute' => 'close_time',
                    'content' => function ($model) {
                        return (!empty($model->close_time) ? date('H:i:s', $model->close_time) : '');
                    },
                ],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>

