<?php
use yii\helpers\Html;


// Параметры/настройки для виджета в js


// эти параметры переезжаются в html аттрибуты
$js = '
    var element_id = "'.$options['id'].'";

    if(sw_setting == void 0) {
        var sw_setting = [];
    }
';

if(isset($ajax['url'])) {
    $js .= '
        sw_setting[element_id] = {
            ajax_url: "' . $ajax['url'] . '",
            element_id: element_id,
            sw_is_open: false
        };
    ';
}elseif(count($list_data) > 0) {

    $js .= 'var list_data = {};';
    foreach($list_data as $key => $val) {
        $js .= 'list_data['.$key.']="'.$val.'";';
    }
    //$js .= 'console.log(list_data);';

    $js .= '
        sw_setting[element_id] = {
            element_id: element_id,
            sw_is_open: false,
            list_data: list_data
        };
    ';
}

//echo "data:<pre>"; print_r($ajax['data']);


if(isset($ajax['data'])) {
    $js .= 'sw_setting[element_id].ajax_data = '.$ajax['data'].';';
}
//if(isset($ajax['afterRequest'])) {
//    $js .= 'sw_setting[element_id].afterRequest = '.$afterRequest.';';
//}
if(!empty($afterRequest)) {
    $js .= 'sw_setting[element_id].afterRequest = '.$afterRequest.';';
}
if(!empty($afterChange)) {
    $js .= 'sw_setting[element_id].afterChange = '.$afterChange.';';
}
if(!empty($open_url)) {
    $js .= 'sw_setting[element_id].open_url = '.$open_url;
}
if(!empty($add_new_value_url)) {
    $js .= 'sw_setting[element_id].add_new_value_url = '.$add_new_value_url;
}


$this->registerJs($js, \yii\web\View::POS_END);

?>

<div class="form-control sw-element"
     attribute-name = "<?= $name ?>" <?= (isset($options['disabled']) ? 'disabled="'.$options['disabled'].'" ' : '') ?>
     <?= (isset($ajax['url']) ? ' ajax_url = "'.$ajax['url'].'" ' : '') ?>
     sw_is_open = "false"
    >
    <span class="sw-text">
        <?php if($using_delete_button == true) { ?>
            <span class="sw-delete glyphicon glyphicon-remove" <?= (empty($value) ? 'style="display: none;"' : '') ?>></span>
        <?php } ?>
        <?php if(!empty($open_url)) { ?>
            <span class="sw-open glyphicon glyphicon-eye-open" <?= (empty($value) ? 'style="display: none;"' : '') ?>></span>
        <?php } ?>
        <button class="sw-value">
            <?php
            if(!empty($initValueText)) {
                echo $initValueText;
            }else {
                if(!empty($value)) {
                    echo $value;
                }else {
                    if(isset($options['placeholder'])) {
                        echo '<span class="sw-placeholder">' . $options['placeholder'] . '</span>';
                    }
                }
            }
            ?>
        </button>
    </span>
    <span class="select2-selection__arrow" role="presentation">
        <b></b>
    </span>
    <?= Html::hiddenInput($name, $value, $options) ?>
</div>
<div class="sw-outer-block" attribute-name="<?= $name ?>" style="display: none;">
    <div class="sw-inner-block">
        <input type="text" class="sw-search form-control">
        <div class="sw-select-block" style="display: none;">
            <!-- сюда можно засунуть preload-рисунок или что-то подобное -->
        </div>
    </div>
</div>