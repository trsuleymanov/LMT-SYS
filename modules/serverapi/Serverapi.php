<?php

namespace app\modules\serverapi;

use yii\base\Module;

class Serverapi extends Module
{
    public $controllerNamespace = 'app\modules\serverapi\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false; // сессия и куки отключаются в пределах модуля
    }
}
