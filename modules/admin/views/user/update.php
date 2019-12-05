<?php
use yii\helpers\Html;

$this->title = 'Редактирование пользователя &laquo;' . $model->lastname . ' ' . $model->firstname . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
