<?php

use yii\helpers\Html;

$this->title = 'Редактирование тарифа ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Тарифы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="tariff-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
