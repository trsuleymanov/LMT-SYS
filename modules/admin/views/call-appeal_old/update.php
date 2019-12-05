<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CallAppeal */

$this->title = 'Update Call Appeal: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Call Appeals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="call-appeal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
