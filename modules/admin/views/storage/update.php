<?php

use yii\helpers\Html;


$this->title = 'Редактирование склада';
$this->params['breadcrumbs'][] = ['label' => 'Склады', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="storage-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
