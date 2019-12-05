<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\helpers\table\PageSizeHelper;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);


$this->title = 'Агенты АТС';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="operator-beeline-subscription-index" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('Добавить агента', ['create'], ['class' => 'btn btn-success']) ?>
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
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'operator_id',
                [
                    'attribute' => 'name',
                    'label' => 'Название аккаунта',
                    'content' => function ($model) {
                        return (!empty($model->name) ? $model->name : '');
                    }
                ],
                'mobile_ats_login',
                [
                    'attribute' => 'operator_id',
                    'label' => 'Текущий агент',
                    'content' => function ($model) {
                        return (!empty($model->operator_id) ? $model->user->username.' статус: '.$model->status : '');
                    }
                ],
                //'subscription_id',
                //'expire_at',
                [
                    'label' => 'Подписка',
                    'attribute' => 'subscription_id',
                    'content' => function ($model) {

                        //return $model->subscription_id > 0 ? 'истекает '.date('d.m.Y', $model->expire_at) : 'не существует';
                        if(!empty($model->subscription_id)) {
                            return 'истекает '.date('d.m.Y', $model->expire_at)
                                .'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::a('Проверить существование в АТС', [''], ['class' => 'btn btn-info btn-xs check-subsription'])
                                .'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::a('Удалить подписку', [''], ['class' => 'btn btn-danger btn-xs delete-subsription']);
                        }else {
                            return 'не существует';
                                //.'&nbsp;&nbsp;&nbsp;'.Html::a('Создать', [''], ['class' => 'btn btn-success btn-xs create-subsciption']);
                        }
                    },
                ],
                'minutes',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>
