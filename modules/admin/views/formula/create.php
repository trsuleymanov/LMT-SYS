<?php

use yii\helpers\Html;

$this->title = 'Добавление формулы';
$this->params['breadcrumbs'][] = ['label' => 'Формулы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formula-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
