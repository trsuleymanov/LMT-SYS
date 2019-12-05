<?php

namespace app\assets;

use yii\web\AssetBundle;

class ChatWidgetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/chat-widget.css',
    ];
    public $js = [
        'js/chat-widget.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}