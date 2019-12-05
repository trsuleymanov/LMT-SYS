<?php
use app\models\DetailMeasurementValue;
use app\models\TransportModel;
use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\City;
use yii\helpers\ArrayHelper;
use app\helpers\table\PageSizeHelper;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use app\models\Formula;
use app\models\NomenclatureDetail;


$this->title = 'Номенклатура деталей';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
?>
<div id="nomenclature-detail-page" class="box box-default" >
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
            'layout'=>"{summary}\n{items}",
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'name',
                [
                    'label' => 'Наименование',
                    'attribute' => 'temp_name',
                    'content' => function ($model) {
                        return $model->detailName != null ? $model->detailName->name : '';
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'temp_name',
                        'name' => 'temp_name',
                        'initValueText' => ($searchModel->detailName != null ? $searchModel->detailName->name : ''),
                        'options' => [
                            'placeholder' => 'Введите название...',
                        ],
                        'ajax' => [
                            'url' => '/storage/nomenclature-detail/ajax-get-names',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => false
                    ])
                ],
                [
                    'attribute' => 'model_id',
                    'content' => function ($model) {
                        return $model->model_id > 0 ? $model->transportModel->name : '';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'model_id',
                        ['' => 'Все'] + ArrayHelper::map(TransportModel::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'installation_place',
                    'label' => 'Место установки',
                    'content' => function ($model) {
                        //return (empty($model->installation_side) ? '' : $model->formula->name);
                        return $model->installationPlace;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'installation_place',
                        ['' => 'Все'] + NomenclatureDetail::getInstallationPlaces(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'installation_side',
                    'label' => 'Сторона установки',
                    'content' => function ($model) {
                        //return (empty($model->installation_side) ? '' : $model->formula->name);
                        return $model->installationSide;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'installation_side',
                        ['' => 'Все'] + NomenclatureDetail::getInstallationSides(),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'detail_code',
                    'content' => function ($model) {
                        return $model->detail_code;
                    }
                ],
                [
                    'attribute' => 'comment',
                    'content' => function ($model) {
                        return $model->comment;
                    }
                ],
                [
                    'attribute' => 'measurement_value_id',
                    'content' => function ($model) {
                        return $model->measurement_value_id > 0 ? $model->detailMeasurementValue->name : '';
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'measurement_value_id',
                        ['' => 'Все'] + ArrayHelper::map(DetailMeasurementValue::find()->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
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
