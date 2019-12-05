<?php

use yii\helpers\Html;

$this->title = 'Добавление клиента';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
