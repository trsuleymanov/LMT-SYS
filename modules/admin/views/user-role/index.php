<?php

use app\models\Direction;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Роли пользователей';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="user-role-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить роль', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <!--
        ... class="pull-left"
        -->
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'name',
                'alias',
                'description',
                [
                    'attribute' => 'controlled',
                    'content' => function($model) {
                        return ($model->controlled == 0 ? '-' : 'да');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'controlled',
                        ['' => 'Все', 0 => 'Нет', 1 => 'Да'],
                        ['class' => "form-control"]
                    )
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>
