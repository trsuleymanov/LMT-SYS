<?php

use yii\helpers\Html;

$this->title = 'Добавление признака';
$this->params['breadcrumbs'][] = ['label' => 'Список признаков', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="do-tariff-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
