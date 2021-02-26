<?php

use app\helpers\table\PageSizeHelper;
use app\models\Direction;
use app\models\UserRole;
use app\models\WorkingShift;
use app\models\WorkingShiftUnlockingTime;
use app\widgets\SelectWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\LinkPager;


$this->title = 'Контроль блокировки телефона';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<a href="/admin/user-role" target="_blank">Роли пользователей</a><br />
<div id="working-shift-page" class="box box-default">

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

                //'id',
                [
                    'attribute' => 'user_id',
                    'content' => function($model) {
                        return ($model->user != null ? $model->user->fullname : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'user_id',
                        'value' => ($searchModel->user_id > 0 && $searchModel->user != null ? $searchModel->user->fullname : ''),
                        'options' => [
                            'placeholder' => 'Введите ФИО',
                        ],
                        'ajax' => [
                            'url' => '/user/ajax-get-usernames2',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],
                //'shift_type',
                [
                    'attribute' => 'shift_type',
                    'content' => function($model) {
                        return WorkingShift::getShiftTypes()[$model->shift_type];
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'shift_type',
                        ['' => 'Все'] + WorkingShift::getShiftTypes(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'start_time',
                    'content' => function($model) {
                        return ($model->start_time > 0 ? date("d.m.Y H:i", $model->start_time) : '');
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'finish_time',
                    'content' => function($model) {
                        return ($model->finish_time > 0 ? date("d.m.Y H:i", $model->finish_time) : '');
                    },
                    'filter' => false,
                ],

                [
                    'attribute' => 'unlocking_time',
                    'label' => 'Время разблокировки',
                    'content' => function($model) {
                        //return ($model->finish_time > 0 ? date("d.m.Y H:i", $model->finish_time) : '');

                        $aUnlockingTimes = [];
                        $unlocking_times = WorkingShiftUnlockingTime::find()
                            ->where(['working_shift_id' => $model->id])
                            ->all();
                        foreach ($unlocking_times as $unlocking_time) {
                            $aUnlockingTimes[] = date("H:i", $unlocking_time->created_at);
                        }
                        return implode(', ', $aUnlockingTimes);

                    },
                    'filter' => false,
                ],

                // ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
