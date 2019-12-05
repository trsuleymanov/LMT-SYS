<?php

namespace app\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    //public $sourcePath = '@vendor/bower/components-font-awesome';
    public $sourcePath = '@vendor/bower-asset/components-font-awesome';

    public $css = [
        'css/font-awesome.min.css'
    ];
}
