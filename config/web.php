<?php

use app\models\SocketDemon;

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'site',
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'as AccessBehavior' => [    // проверка прав доступа
        'class' => 'app\modules\access\behaviors\AccessBehavior',
    ],
    //'on beforeRequest' => ['common\modules\access\behaviors\TestBehavior'],
    'on '. yii\web\Controller::EVENT_BEFORE_ACTION => function ($event) {
        Yii::$app->params['socket_messages'] = [];
    },
    'on '. yii\web\Controller::EVENT_AFTER_ACTION => function ($event) {

        // сюда попадаем массив сокет сообщений задержащихся в буфере
        // нужно идентичные сообщения схлопнуть, и результат отправить демону на обрабтку
        $aMessages = SocketDemon::optimizeBrowserMessages(Yii::$app->params['socket_messages']);
        foreach($aMessages as $aMessage) {
            SocketDemon::sendOutBrowserMessage($aMessage['page_code'], $aMessage['url_params'], $aMessage['command'], $aMessage['data'], false, $aMessage['usersIds']);
        }
        //return true;
    },
    'modules' => [
        'access' => [
            'class' => 'app\modules\access\Access'
        ],
        'admin' => [
            'class' => 'app\modules\admin\Admin'
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'api' => [
            'class' => 'app\modules\api\Api',
        ],
        'megafon' => [
            'class' => 'app\modules\megafon\Megafon'
        ],
        'beeline' => [
            'class' => 'app\modules\beeline\Beeline'
        ],
        'serverapi' => [
            'class' => 'app\modules\serverapi\Serverapi',
        ],
        'storage' => [
            'class' => 'app\modules\storage\Storage'
        ],
        'waybill' => [
            'class' => 'app\modules\waybill\Waybill'
        ],

        'dynagrid' =>  [
            'class' => '\kartik\dynagrid\Module',
            // other settings (refer documentation)
        ],
    ],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/user',
                    'pluralize' => false,
                ],
                '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_m>/<_c>/<_a>',
                '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'gkyKJ5yPHTCI8C_YsDfZDQhx6ecPenZo',
            'enableCsrfValidation' => false,

            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            //'enableAutoLogin' => false,
            //'enableSession' => false
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
//        'command' => [
//            'class' => 'yii\db\Command',
//            'fetchMode' => \PDO::FETCH_OBJ,
//        ],

        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
        ],

        'setting' => [
            'class' => 'app\components\Setting',
        ]
    ],
    'params' => $params,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['128.0.0.1', '::1',],
        //'allowedIPs' => ['185.6.83.45:7900', '127.0.0.1', '127.0.0.0'],
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1',],
        //'allowedIPs' => ['*']
    ];
}



return $config;
