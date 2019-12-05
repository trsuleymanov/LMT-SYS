<?php

use yii\helpers\Html;


$this->title = 'Создание единицы измерения';
$this->params['breadcrumbs'][] = ['label' => 'Единицы измерения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detail-measurement-value-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
