<?php
use yii\helpers\Html;

$this->title = 'Редактирование расписания: &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['/admin/direction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Направление ' . $model->direction->sh_name, 'url' => ['/admin/direction/update', 'id' => $model->direction_id]];
$this->params['breadcrumbs'][] = 'Изменение расписания';
?>
<div class="schedule-update">
    <?= $this->render('_form', [
        'model' => $model,
        'searchTripModel' => $searchTripModel,
        'dataTripProvider' => $dataTripProvider,
    ]) ?>
</div>
