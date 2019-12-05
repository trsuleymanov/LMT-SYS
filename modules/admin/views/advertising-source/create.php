<?php

$this->title = 'Добавление рекламного источника';
$this->params['breadcrumbs'][] = ['label' => 'Источники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advertising-source-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
