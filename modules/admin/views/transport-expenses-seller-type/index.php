<?php

use app\helpers\table\PageSizeHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Типы продавца';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="transport-expenses-seller-type-index" class="box box-default">
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить тип продавца', ['create'], ['class' => 'btn btn-success']) ?>
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

                'id',
                'name',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'options' => ['style' => 'width: 50px;'],
                    'buttons' => [
                        'update' => function ($url, $model)
                        {
                            if($model->name == 'Введите название') {
                                return '';
                            }else {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/transport-expenses-seller-type/update', 'id' => $model->id]),
                                    [
                                        'title' => 'Редактировать',
                                        'aria-label' => 'Редактировать'
                                    ]
                                );
                            }
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
