<?php
use yii\helpers\Html;

$this->title = 'Добавление кэш-бэка';
$this->params['breadcrumbs'][] = ['label' => 'Список кэш-бэков', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashback-setting-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
