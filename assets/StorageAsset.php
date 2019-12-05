<?php
namespace app\assets;

use yii\web\AssetBundle;

class StorageAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/storage.css',
        'css/site_old.css',
        'css/disper.css',
        //'css/bootstrap-select.css',
        'css/site.css',
        'css/main.css',
        'css/select-widget.css',
        'css/popup-form-widget.css',
        'css/editable-text-widget.css',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css',
    ];
    public $js = [
        'js/storage/storage.js',
        'js/main.js',
//        'js/site/site.js',
        'js/select-widget.js',
//        'js/popup-form-widget.js',
//        'js/editable-text-widget.js',
//        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
//        'js/datepicker-ru.js',
//        'js/jquery.maskedinput.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
