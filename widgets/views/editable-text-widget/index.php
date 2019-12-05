<?php

// Параметры/настройки для виджета в js
$js = '
    var elem_id = "'.$options['id'].'";
    if(typeof(etw_setting) == "undefined") {
        etw_setting = {};
    }
    etw_setting[elem_id] = {
        elem_id: elem_id
    };
';

if(!empty($onChange)) {
    $js .= 'etw_setting[elem_id].onChange = '.$onChange.';';
}
if(!empty($mask)) {
    $js .= 'etw_setting[elem_id].mask = "'.$mask.'";';
}

$this->registerJs($js, \yii\web\View::POS_END);

$options_params = [];
if(isset($options)) {
    foreach($options as $op_param => $op_value) {
        if($op_param != 'class') {
            $options_params[] = $op_param.'="'.$op_value.'"';
        }
    }
}


?><a class="etw-element<?= (isset($options['class']) ? ' '.$options['class'] : '') ?>" <?= implode(' ', $options_params) ?>><?= (!empty($value) ? $value : $defaultValue) ?></a>
<div class="etf-block" for="<?= $options['id'] ?>" style="display: none;">
    <div class="etf-input">
        <input type="text" name="<?= $name?>"  value="<?= $value ?>">
        <span class="etf-clear-x"></span>
    </div>
    <div class="etf-buttons">
        <a class="btn btn-primary etf-but-accept" href="#"><i class="glyphicon glyphicon-ok"></i></a>
        <a class="btn btn-default etf-but-cancel" href="#"><i class="glyphicon glyphicon-remove"></i></a>
    </div>
</div>