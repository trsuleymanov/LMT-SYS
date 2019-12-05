<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site_old.css',
	    'css/disper.css',
        //'css/bootstrap-select.css',
        'css/site.css',
        'css/main.css',
        'css/select-widget.css',
        'css/popup-form-widget.css',
        'css/editable-text-widget.css',
        //'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css',
        'css/jquery-ui.css'
    ];
    public $js = [
        //'js/socket.io.js',
        'js/main.js',
        'js/site/site.js',
        'js/select-widget.js',
        'js/popup-form-widget.js',
        'js/editable-text-widget.js',
        //'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        'js/jquery-ui.min.js',
        'js/datepicker-ru.js',
	    'js/jquery.maskedinput.js',
        'js/imask.js',
        //'https://api-maps.yandex.ru/1.1/index.xml' // для работы яндекс-карты
        //'http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU'
        //'https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU' //для работы яндекс-карты
        //'https://api-maps.yandex.ru/2.1/?lang=ru_RU'
        'js/site/trip.js',
        'js/site/modalAddTripTransport.js',
        'js/ion-sound/js/ion.sound.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
