<?php

use yii\helpers\Html;

$this->title = 'Редактирование роли &laquo;' . $model->name. '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Роли', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="user-role-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
