<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
//        'user' => [
//            'class' => 'yii\web\User',
//            'identityClass' => 'app\models\User',
//            //'enableAutoLogin' => true,
//        ],
//        'session' => [ // for use session in console application
//            'class' => 'yii\web\Session'
//        ],
//        'request' => [
//            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
//            'cookieValidationKey' => 'gkyKJ5yPHTCI8C_YsDfZDQhx6ecPenZo',
//            'enableCookieValidation' => true,
//            'enableCsrfValidation' => false,
//
//            'parsers' => [
//                'application/json' => 'yii\web\JsonParser',
//            ],
//        ],
    ],
    'params' => $params,
    'aliases' => [
        '@webroot' => dirname(dirname(__FILE__)) . '/web',
        //'@web' => dirname(dirname(__FILE__)) . '/web',
        //'@web' => exec('hostname')
        '@web' => 'http://tobus-yii2.ru/',
        '@bower' => dirname(dirname(__FILE__)) . '/vendor/bower-asset',
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],
    ],
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
