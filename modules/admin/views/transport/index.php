<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\City;
use yii\helpers\ArrayHelper;
use app\helpers\table\PageSizeHelper;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);

$this->title = 'Машины';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();

?>
<div id="transport-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить машину', ['create'], ['class' => 'btn btn-success']) ?>
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
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'active',
                    'label' => 'Акт.',
                    'content' => function ($model) {
                        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                            return '<input class="transport-active" transport-id=' . $model->id . ' ' . ($model->active == true ? 'checked' : '') . ' type="checkbox">';
                        }else {
                            return ($model->active == 1 ? 'да' : 'нет');
                        }
                    },
                ],
                [
                    'attribute' => 'accountability',
                    'content' => function ($model) {
                        if(in_array(Yii::$app->session->get('role_alias'), ['root', 'admin'])) {
                            return '<input class="transport-accountability" transport-id=' . $model->id . ' ' . ($model->accountability == true ? 'checked' : '') . ' type="checkbox">';
                        }else {
                            return ($model->accountability == 1 ? 'да' : 'нет');
                        }
                    },
                ],
                'model',
                //'sh_model',
                'car_reg',
                [
                    'attribute' => 'places_count',
                    'label' => 'Мест',
                ],
                'color',
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
                    ])
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
                    'attribute' => 'base_city_id',
                    'label' => 'ГБ',
                    'content' => function ($model) {
                        return (empty($model->base_city_id) ? '' : $model->baseCity->name);
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'base_city_id',
                        ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'formula_id',
                    'content' => function ($model) {
                        return (empty($model->formula_id) ? '' : $model->formula->name);
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'formula_id',
                        ['' => 'Все'] + ArrayHelper::map(\app\models\Formula::find()->all(), 'id', 'name'),
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
