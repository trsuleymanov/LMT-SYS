<?php
use app\helpers\table\PageSizeHelper;
use app\models\Driver;
use app\widgets\SelectWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\LinkPager;
use app\widgets\periodPicker\PeriodPicker;


$this->title = 'Фотографии отправленные водителями';
$this->params['breadcrumbs'][] = $this->title;

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();


?>
<div id="driver-photo-page" class="box box-default">

    <div class="box-header scroller with-border">
        <div class="pull-left"></div>

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
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'transport_car_reg',
                    'content' => function($model) {
                        return (!empty($model->transport_car_reg) ? $model->transport_car_reg : '');
                    }
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'Владелец телефона',
                    'content' => function($model) {
                        return ($model->userdriver != null ? $model->userdriver->fio : $model->user_id);
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'user_id',
                        'value' => ($searchModel->user_id > 0 && $searchModel->userdriver != null ? $searchModel->userdriver->fio : ''),
                        'options' => [
                            'placeholder' => 'Введите ФИО',
                        ],
                        'ajax' => [
                            'url' => '/admin/driver/ajax-get-drivers?field_key=user_id',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],
                [
                    'attribute' => 'driver_id',
                    'content' => function($model) {
                        return (!empty($model->driver_id) && $model->driver != null ? $model->driver->fio : '');
                    },
                    'filter' => SelectWidget::widget([
                        'model' => $searchModel,
                        'attribute' => 'driver_id',
                        'name' => 'user_id',
                        'value' => ($searchModel->driver_id > 0 && $searchModel->driver != null ? $searchModel->driver->fio : ''),
                        'options' => [
                            'placeholder' => 'Введите ФИО',
                        ],
                        'ajax' => [
                            'url' => '/admin/driver/ajax-get-drivers',
                            'data' => new JsExpression('function(params) {
                                return {
                                    search: params.search,
                                };
                            }'),
                        ],
                        'using_delete_button' => true
                    ])
                ],
                [
                    'attribute' => 'time_loading_finish',
                    'label' => 'Время выгрузки',
                    'content' => function($model) {
                        return (!empty($model->time_loading_finish) ? date('d.m.Y H:i', $model->time_loading_finish) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_loading_finish',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],
                [
                    'attribute' => 'photo_created_on_mobile',
                    'label' => 'Время создания фото на телефоне',
                    'content' => function($model) {
                        return (!empty($model->photo_created_on_mobile) ? date('d.m.Y H:i', $model->photo_created_on_mobile) : '');
                    },
                    'filter' => PeriodPicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'photo_created_on_mobile',
                        'isFilterInGridView' => true,
                        'clearButton' => false
                    ])
                ],

                [
                    'attribute' => 'photo_link',
                    'content' => function($model) {
                        return '<a href="'.$model->photo_link.'" data-lightbox="example-1"><img class="thumb-img" src="'.$model->photo_link.'" /></a>';
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
