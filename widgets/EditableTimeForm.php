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

class EditableTimeForm extends InputWidget
{
    public function run()
    {
        $aHours = [
            '',
            '00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07',
            '08' => '08', '09' => '09', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15',
            '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23'
        ];

        $aMinutes = [];
        $aMinutes[] = '';
        for($m = 0; $m < 60; $m++) {
            $min = $m;
            if($min < 10) {
                $min = '0'.$min;
            }
            $aMinutes[$min] = $min;
        }

        $hours = '';
        $minutes = '';


        if ($this->hasModel()) {
            $this->name = empty($this->options['name']) ? Html::getInputName($this->model, $this->attribute) : $this->options['name'];
            $this->value = Html::getAttributeValue($this->model, $this->attribute);

            // полученное значение - это timestamp (по идее)
            if(!preg_match('/^[0-9]{2}:[0-9]{2}$/i', $this->value)) {
                $this->value = date('H:i', $this->value);
            }
        }

        if(strpos($this->value, ':') !== false) {
            $hours_minutes = explode(':', $this->value);
            $hours = $hours_minutes[0];
            $minutes = $hours_minutes[1];
        }

        return $this->render('editable-time-form/index', [
            'aHours' => $aHours,
            'aMinutes' => $aMinutes,
            'hours' => $hours,
            'minutes' => $minutes,
            'attribute_name' => $this->name
        ]);
    }
}