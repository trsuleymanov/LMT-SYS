<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;

$this->title = 'Сокет ip-адреса';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();


?>
<div id="socket-ip-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить ip-адрес', ['create'], ['class' => 'btn btn-success']) ?>
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
        <?php

        echo GridView::widget([
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
                [
                    'attribute' => 'ip',
                    'label' => 'ip адрес сервера с портом (например 185.148.219.40:19841)',
                    'content' => function ($model) {
                        return $model->ip;
                    },
                ],
                'description',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} ',
                    'options' => ['style' => 'width: 25px;']
                ],
            ],
        ]);
        ?>
    </div>
</div>
