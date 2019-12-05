<?php
use yii\helpers\Html;

$schedule = $model->schedule;

$this->title = 'Редактирование рейса: &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['/admin/direction/index']];
$this->params['breadcrumbs'][] = ['label' => 'Направление ' . $schedule->direction->sh_name, 'url' => ['/admin/direction/update', 'id' => $schedule->direction_id]];
$this->params['breadcrumbs'][] = ['label' => 'Расписание &laquo;' . $schedule->name . '&raquo;', 'url' => ['/admin/schedule/update', 'id' => $schedule->id]];
$this->params['breadcrumbs'][] = 'Изменение рейса';

?>
<div class="schedule-trip-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
