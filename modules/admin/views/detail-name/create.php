<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DetailName */

$this->title = 'Create Detail Name';
$this->params['breadcrumbs'][] = ['label' => 'Detail Names', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detail-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
