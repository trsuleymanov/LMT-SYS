<?php

use yii\helpers\Html;

$this->title = 'Редактирование типа продавца';
$this->params['breadcrumbs'][] = ['label' => 'Список типов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-expenses-seller-type-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
