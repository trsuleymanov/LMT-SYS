<?php

use yii\helpers\Html;

$schedule = $model->schedule;

$this->title = 'Создание рейса';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['/admin/direction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Направление ' . $schedule->direction->sh_name, 'url' => ['/admin/direction/update', 'id' => $schedule->direction_id]];
$this->params['breadcrumbs'][] = ['label' => 'Расписание &laquo;' . $schedule->name . '&raquo;', 'url' => ['/admin/schedule/update', 'id' => $schedule->id]];
$this->params['breadcrumbs'][] = 'Создание рейса';
?>
<div class="schedule-trip-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
