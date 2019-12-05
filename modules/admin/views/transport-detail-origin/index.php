<?php

use app\helpers\table\PageSizeHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;


$this->title = 'Происхождение деталей';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

?>
<div id="transport-detail-origin-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить происхождение', ['create'], ['class' => 'btn btn-success']) ?>
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
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'name',

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
