<?php

use yii\helpers\Html;

$this->title = 'Добавление машины';
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
