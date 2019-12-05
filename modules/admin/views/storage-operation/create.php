<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\StorageOperation */

$this->title = 'Create Storage Operation';
$this->params['breadcrumbs'][] = ['label' => 'Storage Operations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="storage-operation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
