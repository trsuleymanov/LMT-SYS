<?php

use app\models\Call;
use app\models\CallContact;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Контакты (группы связанных звонков)';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>
<div id="call-contact-page" class="box box-default">

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
                    'label' => 'Звонки',
                    'content' => function ($model) {
                        $calls = $model->calls;

                        return '<a href="#" class="toggle-calls" is-open="false">'.count($calls).'</a>';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (!empty($model->created_at) ? date('d.m.Y H:i:s', $model->created_at) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'initiator',
                    'content' => function ($model) {
                        return (!empty($model->initiator) ? CallContact::getInitiators(true)[$model->initiator] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'initiator',
                        ['' => 'Все'] + CallContact::getInitiators(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'label' => 'Оператор иницировавший звонок',
                    'attribute' => 'initiator_operator_user_id',
                    'content' => function ($model) {
                        return (!empty($model->initiator_operator_user_id) ? $model->initiatorOperator->username : '');
                    }
                ],
                'client_phone',
                [
                    'attribute' => 'operator_user_id',
                    'content' => function ($model) {
                        return (!empty($model->operator_user_id) ? $model->operator->username : '');
                    }
                ],
                [
                    'attribute' => 'completed_at',
                    'content' => function ($model) {
                        return (!empty($model->completed_at) ? date('d.m.Y H:i:s', $model->completed_at) : '<span style="color:red;">не завершен</span>');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'completed_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
