<?php

use app\models\DoTariff;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\widgets\periodPicker\PeriodPicker;

$this->title = 'Источник';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="informer-office-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить источник', ['create'], ['class' => 'btn btn-success']) ?>
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

                'name',
                'code',
                [
                    'attribute' => 'cashless_payment',
                    'content' => function ($model) {
                        return ($model->cashless_payment == 1 ? 'да' : '');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'cashless_payment',
                        ['' => 'Все', '0' => 'нет', '1' => 'да'],
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'do_tariff_id',
                    'content' => function ($model) {
                        return $model->doTariff != null ? $model->doTariff->description : '';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'do_tariff_id',
                        ['' => 'Все'] + ArrayHelper::map(DoTariff::find()->where(['tariff_type' => 'order'])->all(), 'id', 'description'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'created_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ]),
                ],
                [
                    'attribute' => 'updated_at',
                    'content' => function ($model) {
                        return (empty($model->updated_at) ? '' : date('d.m.Y', $model->updated_at));
                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'updated_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
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
