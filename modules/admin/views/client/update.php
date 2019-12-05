<?php

use yii\helpers\Html;

$this->title = 'Редактирование клиента &laquo;' . $model->name . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="client-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
