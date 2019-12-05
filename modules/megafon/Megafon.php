<?php

namespace app\modules\megafon;

use yii\base\Module;

class Megafon extends Module
{
    public $controllerNamespace = 'app\modules\megafon\controllers';

    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false; // сессия и куки отключаются в пределах модуля
    }
}
