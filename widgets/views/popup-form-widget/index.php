<?php

// Параметры/настройки для виджета в js
$js = '
    var elem_id = "'.$options['id'].'";
    if(typeof(pfw_setting) == "undefined") {
        pfw_setting = {};
    }
    pfw_setting[elem_id] = {
        elem_id: elem_id
    };
';

if(!empty($onAccept)) {
    $js .= 'pfw_setting[elem_id].onAccept = '.$onAccept.';';
}
if(!empty($onCancel)) {
    $js .= 'pfw_setting[elem_id].onCancel = '.$onCancel.';';
}

$html = $this->render('_default-form', [
    'name' => $name,
    'value' => $value,
    'options' => $options,
    'defaultValue' => $defaultValue,
    'popupPosition' => $popupPosition,
    'formTitle' => $formTitle,
    'formContent' => $formContent,

    'useDefaultAcceptBut' => $useDefaultAcceptBut,
    'useDefaultCancelBut' => $useDefaultCancelBut,
]);

$this->registerJs($js, \yii\web\View::POS_END);


$options_params = [];
if(isset($options)) {
    foreach($options as $op_param => $op_value) {
        if($op_param != 'class') {
            $options_params[] = $op_param.'="'.$op_value.'"';
        }
    }
}
/*
// когда в button добавляются implode(' ', $options_params), то при обновлении контента начинаются конфикты в js, и в
// частности может не срабатывать click на pfw-element
*/

if(isset($options) && isset($options['open_trip_page']) && $options['open_trip_page'] == true) { ?>
    <a href="/trip/trip-orders?trip_id=<?= $options['trip_id'] ?>" target="_blank" class="pfw-element-link"><?= !empty($value) ? $value : $defaultValue ?></a>
    <br />
<?php }else {
?><button type="button" default-value="<?= $defaultValue ?>" class="pfw-element<?= (isset($options['class']) ? ' '.$options['class'] : '') ?>" ><?= !empty($value) ? $value : $defaultValue ?></button>
<div class="pfw-popup"><?= $html ?></div>
<?php } ?>
