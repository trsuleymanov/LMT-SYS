<?php

use app\models\CallAppeal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Обращения';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

//$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>
<div id="call-appeal-index" class="box box-default">

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
                    'attribute' => 'type',
                    'content' => function ($model) {
                        return (!empty($model->type) ? CallAppeal::getTypes(true)[$model->type] : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'type',
                        ['' => 'Все'] + CallAppeal::getTypes(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'contact_id',
                    'content' => function ($model) {
                        return (!empty($model->contact_id) ? Html::a($model->contact_id,  Url::to(['/admin/call-contact/index?CallContactSearch[id]='.$model->contact_id])) : '');
                    },
                ],
                [
                    'attribute' => 'success_call_id',
                    'content' => function ($model) {
                        return (!empty($model->success_call_id) ? Html::a($model->success_call_id,  Url::to(['/admin/call/index?CallSearch[id]='.$model->success_call_id])) : '');
                    },
                ],
                'client_phone',
                [
                    'attribute' => 'operator_user_id',
                    'content' => function ($model) {
                        return (!empty($model->operator_user_id) ? $model->operator->username : '');
                    },
                    'filter' => false
                ],

                //s['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
