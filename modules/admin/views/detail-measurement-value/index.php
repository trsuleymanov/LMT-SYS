<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;

$this->title = 'Единицы измерения';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="detail-measurement-value-page" class="box box-default" >
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

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'name',
                [
                    'attribute' => 'count_is_double',
                    'content' => function ($model) {
                        return $model->count_is_double == true ? 'да' : 'нет';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'count_is_double',
                        ['' => 'Все', 0 => 'нет', 1 => 'да'],
                        ['class' => "form-control"]
                    )
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}',
                    'options' => ['style' => 'width: 20px;']
                ],
            ],
        ]); ?>

    </div>
</div>
