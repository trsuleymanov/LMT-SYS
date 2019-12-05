<?php
use yii\helpers\Html;

$this->title = 'Действия';

$this->registerJsFile('js/admin/actions.js', ['depends'=>'app\assets\AppAsset']);
?>

<div class="actions">

    <div class="form-group">
        <p>Перед запуском необходимо проверить что существует в проекте директория /backups/ с правами 0777</p>
        <?= Html::button('Создать дамп БД', ['id' => 'dump-database', 'class' => 'btn btn-info']) ?><br />
    </div>
    <div class="form-group">
        <?= Html::button('Создать дамп Склада', ['id' => 'dump-storage', 'class' => 'btn btn-info']) ?><br />
    </div>
    <a id="download-file" style="display: none;" href="">Скачать файл</a>
</div>
