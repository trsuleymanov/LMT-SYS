<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CallEvent */

$this->title = 'Create Call Event';
$this->params['breadcrumbs'][] = ['label' => 'Call Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
