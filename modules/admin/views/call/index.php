<?php

use app\models\Call;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;


$this->title = 'Звонки';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="call-page" class="box box-default">

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
                    'label' => 'Направление',
                    'attribute' => 'call_direction',
                    'content' => function($model) {
                        return $model->call_direction == 'output' ? 'исх' : 'вх';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'call_direction',
                        ['' => 'Все', 'input' => 'Входящие', 'output' => 'Исходящие'],
                        ['class' => "form-control"]
                    )
                ],

                'operand',
                //'t_create',
                [
                    'attribute' => 't_answer',
                    'content' => function($model) {
                        return (!empty($model->t_answer) ? date('d.m.Y H:i:s', $model->t_answer) : '');
                    }
                ],
                //'t_hungup',
                [
                    'attribute' => 't_hungup',
                    'label' => 'Длительность звонка',
                    'content' => function($model) {
                        if(!empty($model->t_hungup) && !empty($model->t_create)) {
                            return ($model->t_hungup - $model->t_create) . ' сек.';
                        }else {
                            return '0 сек.';
                        }
                    }
                ],
                [
                    'attribute' => 't_hungup',
                    'label' => 't_hungup',
                    'content' => function($model) {
                        return (!empty($model->t_hungup) ? date('H:i:s', $model->t_hungup) : '');
                    }
                ],
                //'ats_start_time:datetime',
                //'ats_answer_time', // показывать как время ожидания клиента до начала разговора, либо же до того как он повесил трубку!
                [
                    'attribute' => 'ats_answer_time',
                    'label' => 'Ожидание до начала разговора, либо до завершения дозвона',
                    'content' => function($model) {
                        if($model->ats_answer_time > 0) {
                            return (($model->ats_answer_time - $model->ats_start_time)/1000).' сек.';
                        }else {
                            return (($model->ats_eok_time - $model->ats_start_time)/1000).' сек.';
                        }
                    }
                ],

                //'ats_eok_time:datetime',
                //'ext_tracking_id',
                [
                    'attribute' => 'sip',
                    'content' => function($model) {
                        return (!empty($model->sip) ? $model->sip : '');
                    }
                ],

                //'handling_call_operator_id',
                [
                    'attribute' => 'status',
                    'content' => function ($model) {
                        return (!empty($model->status) ? Call::getStatuses()[$model->status] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status',
                        ['' => 'Все'] + Call::getStatuses(),
                        ['class' => "form-control"]
                    )
                ],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
