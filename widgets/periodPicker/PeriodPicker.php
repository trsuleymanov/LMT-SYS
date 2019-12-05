<?php

namespace app\widgets\periodPicker;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\widgets\InputWidget;

use app\widgets\periodPicker\assets\PeriodPickerAsset;

/**
 * Class PeriodPicker
 * @package app\widgets\periodPicker
 */
class PeriodPicker extends InputWidget
{
    /*****************************************************************************************************************
     *                                                                                               PUBLIC PROPERTIES
     *****************************************************************************************************************/

    /**
     * Опции плагина
     * некоторые основные опции вынесены в свойства виджета
     * http://xdsoft.net/jqplugins/periodpicker/
     * @var array
     */
    public $options = [];
    /**
     * @var bool сабмитить или нет сразу после выбора даты/периода форму, в которой инициализирован виджет
     */
    public $autoSubmit = false;
    /**
     * @var bool отображать или нет кнопку очистки выбора периода
     */
    public $clearButton = true;
    /**
     * @var bool если виджет выводится в фильтре GridView виджета - установите в true для правильной отработки
     */
    public $isFilterInGridView = false;

    public $onOkButtonClick = 'function () { }';

    /*****************************************************************************************************************
     *                                                                                              PRIVATE PROPERTIES
     *****************************************************************************************************************/

    /**
     * @var string идентификаторы скрытых полей
     */
    private $idField, $idStart, $idEnd = null;

    /*****************************************************************************************************************
     *                                                                                                  PUBLIC METHODS
     *****************************************************************************************************************/

    public function run()
    {
        $this->options = ArrayHelper::merge($this->defaultOptions(), $this->options);

        if ($this->hasModel()) {
            $this->value = $this->model->{$this->attribute};
            $this->idField = Html::getInputId($this->model, $this->attribute);

            echo Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->idField]);
        } else {
            $this->idField = uniqid($this->name);

            echo Html::hiddenInput($this->name, $this->value, ['id' => $this->idField]);
        }

        $this->idStart = $this->idField . '-start';
        $this->idEnd = $this->idField . '-end';

        $this->options['end'] = '#' . $this->idEnd;

        $startValue = $endValue = null;
        if (!is_null($this->value) && strpos($this->value, '-') !== false) {
            list($startValue, $endValue) = explode('-', $this->value);
        }

        echo Html::hiddenInput($this->name . '_start', $startValue, ['id' => $this->idStart, 'disabled' => 'disabled']);
        echo Html::hiddenInput($this->name . '_end', $endValue, ['id' => $this->idEnd, 'disabled' => 'disabled']);

        $this->registerAsset();
    }

    /*****************************************************************************************************************
     *                                                                                                 PRIVATE METHODS
     *****************************************************************************************************************/

    /**
     * @return array
     */
    private function defaultOptions()
    {
        $defaultOptions = [
            'timepicker' => true,
            'timepickerOptions' => [
                'hours' => true,
                'minutes' => true,
                'seconds' => false,
                'ampm' => false,
                'twelveHoursFormat' => false,
                'defaultTime' => '00:00'
            ],
            'defaultEndTime' => '23:59',
            'tabIndex' => 0,
            'formatDate' => 'DD.MM.YYYY',
            'formatDateTime' => 'DD.MM.YYYY HH:mm',
            'norange' => false,
            'lang' => 'ru',
            'i18n' => [
                'ru' => [
                    'Clear' => 'Очистить',
                    'Today' => 'Сегодня'
                ]
            ],
            //'clearButton' => true,
            //'closeAfterClear' => true,
            'clearButtonInButton' => $this->clearButton ? true : false,
            'todayButton' => true,
            //'animation' => true,
            //'showDatepickerInputs' => false,
        ];

        if ($this->isFilterInGridView) {
            $defaultOptions = ArrayHelper::merge($defaultOptions, [
                'formatDecoreDate' => 'D.MM',
                'formatDecoreDateWithYear' => 'D.MM.YY',
                'timepicker' => false,
                'draggable' => false,
                'i18n' => [
                    'ru' => ['Choose period'=> 'за всё время']
                ]
            ]);
        }

        if (!isset($this->options['cells'])) {
            $defaultOptions['cells'] = [1, 3];
        }

        return $defaultOptions;
    }

    private function registerAsset()
    {
        PeriodPickerAsset::register($this->view);

        $options = Json::encode($this->options);

        $idStart = $this->idStart;

        $id = $this->idField;

        $submit = $this->autoSubmit ? 1 : 0;

        $filter = $this->isFilterInGridView ? 1 : 0;

        $js = <<<JS
var on = {
    onAfterHide: function(){
        var input = $('#{$id}'),
            oldValue = input.val(),
            newValue = $('#{$idStart}').periodpicker('valueStringStrong');
        if ({$filter}) {
            input.closest('.grid-view').on('beforeFilter', function(event){
                return true;
            });
        }
        if (oldValue != newValue) {
            input.val(newValue);
            if ({$submit}) {
                input.closest('form').submit();
            } else if ({$filter}) {
                input.closest('.grid-view').yiiGridView('applyFilter');
            }
        }
    },
    onClearButtonClick: function(){
        var input = $('#{$id}'),
            oldValue = input.val(),
            newValue = $('#{$idStart}').periodpicker('valueStringStrong');
        if (oldValue != newValue) {
            input.val(newValue);
            if ({$submit}) {
                input.closest('form').submit();
            }
        }
    },
    onAfterShow: function(){
        if ({$filter}) {
            $('#{$id}').closest('.grid-view').on('beforeFilter', function(event) {
                return false;
            });
        }
    },
    onChangePeriod: function () {
        console.log('change');
    },
    onOkButtonClick: {$this->onOkButtonClick}
};
var options = $.extend({$options}, on);
$('#{$idStart}').periodpicker(options);
JS;
        $this->view->registerJs($js);

        $css = <<<CSS
.period_picker_box input{
    line-height:normal;
}
.period_picker_input{
    line-height:30px;
    display:block;
    overflow:hidden;
}
CSS;
        $this->view->registerCss($css);
    }
}
