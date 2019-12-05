<?php

use yii\helpers\Html;

$this->title = 'Редактирование происхождения';
$this->params['breadcrumbs'][] = ['label' => 'Номенклатура', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="transport-detail-origin-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
