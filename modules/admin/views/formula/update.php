<?php

use yii\helpers\Html;


$this->title = 'Редактирование формулы &laquo;'.$model->name.'&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Формулы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="formula-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
