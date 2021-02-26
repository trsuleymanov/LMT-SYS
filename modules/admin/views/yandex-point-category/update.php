<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YandexPointCategory */

$this->title = 'Редактирование категории: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Список категорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="yandex-point-category-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
