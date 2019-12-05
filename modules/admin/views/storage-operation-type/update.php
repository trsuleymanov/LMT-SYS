<?php

use yii\helpers\Html;

$this->title = 'Редактирование вида операции';
$this->params['breadcrumbs'][] = ['label' => 'Виды операций', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="storage-operation-type-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
