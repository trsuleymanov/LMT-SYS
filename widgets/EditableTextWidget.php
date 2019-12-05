<?php
namespace app\widgets;

use Yii;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\widgets\InputWidget;
use yii\web\View;
use yii\base\ErrorException;
use yii\web\UnprocessableEntityHttpException;

/*
 * Виджет добавляет к тексту подчеркивание и при щелчке на текст раскрывается на месте текста поле
 * для редактирования
 */
class EditableTextWidget extends InputWidget
{
    public $defaultValue = 'default value';
    public $mask = '';
    public $onChange = '';

    public function run()
    {
        if(empty($this->options['id'])) {
            throw new ForbiddenHttpException('У каждого элемента PopupWidget должен автоматом назначаться уникальный id');
        }

        if ($this->hasModel()) {
            $this->name = empty($this->options['name']) ? Html::getInputName($this->model, $this->attribute) : $this->options['name'];
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
        }

        return $this->render('editable-text-widget/index', [
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
            'defaultValue' => $this->defaultValue,
            'mask' => $this->mask,
            'onChange' => $this->onChange
        ]);
    }
}