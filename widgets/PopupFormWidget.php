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
 * Виджет при щелчке на строке текста виджета открывает "подсказку"-baloon с формой для изменения значения
 */
class PopupFormWidget extends InputWidget
{
    //public $initValueText = ''; // не обязательный параметр
    public $defaultValue = 'default value';
    public $formTitle = '&nbsp;';
    public $formContent = null;
    public $popupPosition = 'right';
    //public $loadFormContent = ''; // на будущее...
    public $useDefaultAcceptBut = true;
    public $useDefaultCancelBut = true;
    public $onAccept = '';
    public $onCancel = '';

    public function run()
    {
        if($this->formContent == null) {
            $this->formContent = '<input type="text" class="form-control pfw-input" name="'.$this->name.'" value="'./*(!empty($this->initValueText) ? $this->initValueText : $this->value)*/  $this->value.'" />';
        }

        if(empty($this->options['id'])) {
            throw new ForbiddenHttpException('У каждого элемента PopupWidget должен автоматом назначаться уникальный id');
        }

        return $this->render('popup-form-widget/index', [
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
            //'initValueText' => $this->initValueText,
            'defaultValue' => $this->defaultValue,
            'popupPosition' => $this->popupPosition,
            'formTitle' => $this->formTitle,
            'formContent' => $this->formContent,

            'useDefaultAcceptBut' => $this->useDefaultAcceptBut,
            'useDefaultCancelBut' => $this->useDefaultCancelBut,

            'onAccept' => $this->onAccept,
            'onCancel' => $this->onCancel
        ]);
    }
}