<?php

use yii\helpers\Html;


$this->title = 'Создание типа продавца';
$this->params['breadcrumbs'][] = ['label' => 'Список типов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transport-expenses-seller-type-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
