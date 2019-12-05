<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\City;
use yii\helpers\ArrayHelper;


$this->title = 'Направления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="direction-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить направление', ['create'], ['class' => 'btn btn-success']) ?>
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

                'sh_name',
                [
                    'attribute' => 'city_from',
                    'content' => function ($model) {
                        return $model->cityFrom->name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'city_from',
                        ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'city_to',
                    'content' => function ($model) {
                        return $model->cityTo->name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'city_to',
                        ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                'distance',
//                [
//                    'attribute' => 'created_at',
//                    'content' => function ($model) {
//                        return date('d.m.Y', $model->created_at);
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'created_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ]),
//                ],
//                [
//                    'attribute' => 'updated_at',
//                    'content' => function ($model) {
//                        return date('d.m.Y', $model->updated_at);
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'updated_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
//                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>