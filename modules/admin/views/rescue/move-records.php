<?php
use yii\helpers\Html;

$this->registerJsFile('js/admin/move-records.js', ['depends'=>'app\assets\AppAsset']);

// выносяться на страницу поля:
//  ftp-сервер + логин ftp + пароль ftp + путь на ftp (директория) + токен API
?>
<div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label class="control-label">ftp-сервер</label>
            <input id="ftp_server" type="text" placeholder="185.148.219.40" class="form-control" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label class="control-label">ftp логин</label>
            <input id="ftp_login" type="text" class="form-control" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label class="control-label">ftp пароль</label>
            <input id="ftp_password" type="text" class="form-control" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label class="control-label">ftp директория</label>
            <input id="ftp_path" type="text" placeholder="/BEEREC" class="form-control" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 form-group form-group-sm">
            <label class="control-label">токен API</label>
            <input id="beeline_token_api" type="text" class="form-control" />
        </div>
    </div>


    <div class="form-group">
        <input id="move-records" type="button" class="btn btn-info" value="Скачать и перенести 10 записей на другой сервер" />
    </div>
</div>
