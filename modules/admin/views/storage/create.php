<?php

use yii\helpers\Html;


$this->title = 'Создание склада';
$this->params['breadcrumbs'][] = ['label' => 'Склады', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
