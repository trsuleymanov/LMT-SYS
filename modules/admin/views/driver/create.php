<?php

use yii\helpers\Html;

$this->title = 'Добавление водителя';
$this->params['breadcrumbs'][] = ['label' => 'Водители', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
