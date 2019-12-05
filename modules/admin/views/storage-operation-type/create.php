<?php

use yii\helpers\Html;


$this->title = 'Создание вида операции';
$this->params['breadcrumbs'][] = ['label' => 'Виды операций', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-operation-type-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
