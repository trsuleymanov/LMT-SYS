<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Loyality */

$this->title = 'Create Loyality';
$this->params['breadcrumbs'][] = ['label' => 'Loyalities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loyality-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
