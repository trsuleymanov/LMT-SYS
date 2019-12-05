<?php

namespace app\widgets\periodPicker\assets;

use yii\web\AssetBundle;

/**
 * Class PeriodPickerAsset
 * @package common\widgets\periodPicker\assets
 */
class PeriodPickerAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/widgets/periodPicker/assets';
    /**
     * @var array
     */
    public $js = ['js/jquery.periodpicker.full.min.js'];
    /**
     * @var array
     */
    public $css = [
        'css/jquery.periodpicker.min.css',
        'css/jquery.timepicker.min.css'
    ];
    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
