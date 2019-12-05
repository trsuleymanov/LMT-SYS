<?php
use yii\helpers\Html;

$this->title = 'Добавление расписания';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['/admin/direction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Направление ' . $model->direction->sh_name, 'url' => ['/admin/direction/update', 'id' => $model->direction_id]];
$this->params['breadcrumbs'][] = 'Добавление расписания';
?>
<div class="schedule-trip-create">
    <?= $this->render('_form', [
        'model' => $model,
        'searchTripModel' => $searchTripModel,
        'dataTripProvider' => $dataTripProvider,
    ]) ?>
</div>
