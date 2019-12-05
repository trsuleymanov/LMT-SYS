<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CallAppeal */

$this->title = 'Create Call Appeal';
$this->params['breadcrumbs'][] = ['label' => 'Call Appeals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-appeal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
