<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\YandexPointCategory */

$this->title = 'Создание яндекс-точки';
$this->params['breadcrumbs'][] = ['label' => 'Список категорий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yandex-point-category-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
