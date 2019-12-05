<?php

namespace app\modules\api;

use yii\base\Module;

class Api extends Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false; // сессия и куки отключаются в пределах модуля
    }
}
