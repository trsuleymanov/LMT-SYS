<?php

use yii\helpers\Html;

$this->title = 'Добавление направления';
$this->params['breadcrumbs'][] = ['label' => 'Направления', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="direction-create">
    <?= $this->render('_form', [
        'model' => $model,
        'searchScheduleModel' => $searchScheduleModel,
        'dataScheduleProvider' => $dataScheduleProvider,
    ]) ?>
</div>
