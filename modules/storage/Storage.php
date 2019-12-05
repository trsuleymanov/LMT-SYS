<?php

namespace app\modules\storage;

use yii\base\Module;

class Storage extends Module
{
    public $controllerNamespace = 'app\modules\storage\controllers';

    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }
}
