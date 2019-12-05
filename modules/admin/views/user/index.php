<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use app\models\UserRole;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="user-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
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
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'yii\grid\ActionColumn',
                    //'template' => '{update} {delete}',
                    'template' => '{update} ',
                    'options' => ['style' => 'width: 50px;']
                ],

                //'id',
//                [
//                    'attribute' => 'last_login_date',
//                    'content' => function ($model) {
//                        return (empty($model->last_login_date) ? '' : date('d.m.Y', $model->last_login_date));
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'last_login_date',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
//                ],
//                [
//                    'attribute' => 'last_login_date',
//                    'content' => function ($model) {
//                        return (empty($model->last_login_date) ? '' : date('d.m.Y H:i', $model->last_login_date));
//                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'last_login_date',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
//                ],
                'username',
//                [
//                    'attribute' => 'mobile_ats_login',
//                    'content' => function ($model) {
//                        if(empty($model->mobile_ats_login)) {
//                            return '';
//                        }else {
//                            return $model->mobile_ats_login;
//                        }
//                    },
//                ],
//                'auth_key',
//                'password_hash',
                'firstname',
                'lastname',
                'email:email',
                'city',
                'address',
                'phone',
                [
                    'attribute' => 'role_id',
                    'content' => function ($model) {
                        if(empty($model->role_id)) {
                            return '';
                        }else {
                            return $model->userRole->name;
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'role_id',
                        ['' => 'Все'] + ArrayHelper::map(UserRole::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
//                'last_ip',
//                'attempt_count',
//                'attempt_date',
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
                    'attribute' => 'blocked',
                    'content' => function ($model) {
                        return ($model->blocked == 1 ? 'Заблокирован' : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'blocked',
                        ['' => 'Все', '0' => 'Нет', '1' => 'Да'],
                        ['class' => "form-control"]
                    )
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    //'template' => '{update} {delete}',
                    'template' => '{update} ',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>
