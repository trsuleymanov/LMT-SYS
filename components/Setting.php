<?php

namespace app\components;

use yii\base\Component;



/**
 * Настройки из базы переходят в общедоступный компонент Yii::$app->setting->любое_свойство
 */
class Setting extends Component
{
    public $data;

    public function init() {
        parent::init();

        $setting = \app\models\Setting::find()->where(['id' => 1])->one();
        foreach ($setting as $attr => $value) {
            $this->$attr = $value;
        }
    }

    /*
     * Magic
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;

        return true;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }
}
