<?php
namespace app\widgets;

use Yii;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\web\View;
use yii\base\ErrorException;
use yii\web\UnprocessableEntityHttpException;

/*
 * Элемент формы с выпадающим списком и поиском.
 */
class SelectWidget extends InputWidget
{
    /*
     * Текстовое значение отображаемое в поле (если оно не передано, то отображается значение текущего аттрибута формы)
     */
    public $initValueText = ''; // не обязательный параметр

    /*
     * Содержит параметры для формирования ajax-запроса
     * Может содержать в себе параметры:
     * - url - путь запроса на сервер - обязательный параметр
     * - data - список передаваемых параметров в ajax-запросе на сервер  - не обязательный параметр
     */
    public $ajax = [];  // обязательный параметр если не передан $data
    public $data = [];


    public $open_url = ''; // js-код функции возвращающей url для открытия окна при нажатии на глаз

    public $afterRequest;
    public $afterChange = '';// js-код функции вызываемой автоматически после изменения значения в виджете

    public $add_new_value_url = ''; // js-код функции возвращающей url для создания нового значения с уже вставленными в урл параметрами (в том числе с новым значением)

    /*
     * При раскрытии селекта, показывать или нет изначально список до использования поиска
     *  - если установлен true, то список будет загружаться при каждом раскрытии селекта. Также в прелоад-список будет
     * загружено $showPreloadList элементов
     * - если установлено false, то при раскрытии селекта список изначально будет отсутсвовать, отображается только поле поиска
     */
    //public $showPreloadList = true;

    /*
     * Количество элементов загружаемых в preload-список (если в preloadItemsLimit установлен 0, значит нет лимита)
     */
    //public $preloadItemsLimit = 100; // возможно этот параметр уедет в вызов виджета?...

    /*
     * Подсказка для элемента формы (если она передана, то будет отображаться в виде значка вопроса)
     */
    //public $tooltip = '';

    public $using_delete_button = true;


    public function run()
    {
        if(!isset($this->ajax['url']) && !isset($this->data)) {
            throw new UnprocessableEntityHttpException('В SelectWidget не передан параметр [ajax][url] или [data]');
        }

        if ($this->hasModel()) { // нужно передавать и model и attribute чтобы условие выполнилось
            $this->name = empty($this->options['name']) ? Html::getInputName($this->model, $this->attribute) : $this->options['name'];
            if(empty($this->value)) {
                $this->value = Html::getAttributeValue($this->model, $this->attribute);
            }
        }

        //$this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        $this->options['id'] = $this->getId(); // уникальный id, и не ебет



        return $this->render('select-widget/index', [
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
            'initValueText' => $this->initValueText,

            'ajax' => $this->ajax,
            'list_data' => $this->data,
            'open_url' => $this->open_url,
            'afterRequest' => $this->afterRequest,
            'afterChange' => $this->afterChange,
            'add_new_value_url' => $this->add_new_value_url,

            'using_delete_button' => $this->using_delete_button
        ]);
    }
}