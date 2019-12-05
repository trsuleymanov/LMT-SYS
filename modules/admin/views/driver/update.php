<?php

use yii\helpers\Html;

$this->title = 'Редактирование водителя &laquo;' . $model->fio . '&raquo;';
$this->params['breadcrumbs'][] = ['label' => 'Водители', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="driver-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
