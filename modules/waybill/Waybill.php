<?php

namespace app\modules\waybill;

use yii\base\Module;

class Waybill extends Module
{
    public $controllerNamespace = 'app\modules\waybill\controllers';

    public function init()
    {
        parent::init();
        $this->layout = 'main';
    }
}
