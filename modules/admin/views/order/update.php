<?php

use yii\helpers\Html;

$this->title = 'Редактирование заказа ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="order-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
