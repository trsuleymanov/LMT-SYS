<?php

use app\helpers\table\PageSizeHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;

$this->title = 'Наименование деталей';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div class="detail-name-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить деталь', ['create'], ['class' => 'btn btn-success']) ?>
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
                [
                    'attribute' => 'name',
                    'content' => function ($model) {
                        return

                            '<span class="detail-name">'.$model->name.'</span>'
                            .' <button class="btn btn-default btn-xs btn-edit-detail-name">
                                    <span class="glyphicon glyphicon-pencil"></span>
                               </button>'
                            .'<input class="edit-detail-name"  style="display: none;" type="text" value="'.$model->name.'" />';
                    },
                ],


//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    //'template' => '{update} {delete}',
//                    'template' => '{update} ',
//                    'options' => ['style' => 'width: 50px;']
//                ],
            ],
        ]); ?>
    </div>
</div>
