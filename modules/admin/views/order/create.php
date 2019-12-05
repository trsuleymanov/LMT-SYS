<?php

use yii\helpers\Html;

$this->title = 'Добавление заказа';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
