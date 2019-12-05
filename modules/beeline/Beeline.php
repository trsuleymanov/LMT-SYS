<?php

namespace app\modules\beeline;

use yii\base\Module;

class Beeline extends Module
{
    public $controllerNamespace = 'app\modules\beeline\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false; // сессия и куки отключаются в пределах модуля
    }
}
