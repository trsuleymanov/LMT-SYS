<?php

$this->title = 'Добавление ip-адреса';
$this->params['breadcrumbs'][] = ['label' => 'Список адресов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="socket-ip-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
