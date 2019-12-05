<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CallDocking */

$this->title = 'Create Call Docking';
$this->params['breadcrumbs'][] = ['label' => 'Call Dockings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-docking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
