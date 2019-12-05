<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DriverPhoto */

$this->title = 'Create Driver Photo';
$this->params['breadcrumbs'][] = ['label' => 'Driver Photos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-photo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
