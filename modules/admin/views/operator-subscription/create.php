<?php

$this->title = 'Добавление агента';
$this->params['breadcrumbs'][] = ['label' => 'Агенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operator-beeline-subscription-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
