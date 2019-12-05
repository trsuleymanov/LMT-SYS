<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CallCase */

$this->title = 'Create Call Case';
$this->params['breadcrumbs'][] = ['label' => 'Call Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-case-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
