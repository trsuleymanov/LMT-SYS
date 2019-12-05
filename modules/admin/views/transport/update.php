<?php

use yii\helpers\Html;

$this->title = 'Редактирование машины ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Машины', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
