<?php

$this->title = 'Операторы онлайн';

$this->registerJsFile('js/admin/online-operators.js', ['depends'=>'app\assets\AdminAsset']);
?>
<br />
<div id="active-operators-list">
    <?= $this->render('_online-operators-list', [
        'users' => $users
    ]) ?>
</div>

