<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Просмотр расписания: &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['/admin/direction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Направление ' . $model->direction->sh_name, 'url' => ['/admin/direction/update', 'id' => $model->direction_id]];
$this->params['breadcrumbs'][] = 'Просмотр расписания';
?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Расписание
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'label' => 'Направление',
                    'value' => $model->direction->sh_name
                ],
                'name',
                [
                    'label' => 'Дата запуска расписания',
                    'value' => date('d.m.Y', $model->start_date)
                ],
                [
                    'label' => 'Дата запуска расписания',
                    'value' => ($model->disabled_date > 0 ? date('d.m.Y', $model->disabled_date) : '-')
                ],
            ],
        ]) ?>
    </div>
</div>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Рейсы расписания
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">
        <?= $this->render('index', [
            'searchTripModel' => $searchTripModel,
            'dataTripProvider' => $dataTripProvider,
            'edit_delete_trips' => false
        ]); ?>
    </div>
</div>