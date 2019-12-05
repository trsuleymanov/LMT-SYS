<?php

use yii\helpers\Html;

$this->title = 'Создание модели';
$this->params['breadcrumbs'][] = ['label' => 'Модели', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-model-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
