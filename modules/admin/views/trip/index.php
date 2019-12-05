<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\Direction;

$this->title = 'Рейсы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="trip-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить маршрут', ['create'], ['class' => 'btn btn-success']) ?>
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
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'name',
                [
                    'attribute' => 'date',
                    'content' => function($model) {
                        return date('d.m.Y H:i', $model->date);
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],
                [
                    'attribute' => 'direction_id',
                    'content' => function($model) {
                        return $model->direction->sh_name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'direction_id',
                        ['' => 'Все'] + ArrayHelper::map(Direction::find()->all(), 'id', 'sh_name'),
                        ['class' => "form-control"]
                    )
                ],
                'start_time',
                'mid_time',
                'end_time',
                /*[
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],
                [
                    'attribute' => 'updated_at',
                    'content' => function ($model) {
                        return (empty($model->updated_at) ? '' : date('d.m.Y', $model->updated_at));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],*/

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>
