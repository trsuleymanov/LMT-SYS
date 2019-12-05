<?php
$this->title = 'Добавление магического устройства';
$this->params['breadcrumbs'][] = ['label' => 'Магические устройства', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="magic-device-code-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
