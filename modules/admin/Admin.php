<?php

namespace app\modules\admin;

use yii\base\Module;

class Admin extends Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }
}
