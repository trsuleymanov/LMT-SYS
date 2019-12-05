<?php

use yii\helpers\Html;

$this->title = 'Добавление источника';
$this->params['breadcrumbs'][] = ['label' => 'Источники', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informer-office-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
