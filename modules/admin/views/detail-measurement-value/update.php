<?php

use yii\helpers\Html;


$this->title = 'Редактирование единицы измерения';
$this->params['breadcrumbs'][] = ['label' => 'Единицы измерения', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="detail-measurement-value-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
