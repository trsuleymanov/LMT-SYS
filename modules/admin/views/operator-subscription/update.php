<?php

$this->title = 'Редактирование агента ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Агенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="operator-beeline-subscription-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
