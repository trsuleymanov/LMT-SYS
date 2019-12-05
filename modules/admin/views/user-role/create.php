<?php

use yii\helpers\Html;

$this->title = 'Добавление пользовательской роли';
$this->params['breadcrumbs'][] = ['label' => 'Роли', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-role-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
