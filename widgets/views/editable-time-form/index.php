<?php
use yii\helpers\Html;

?>
<?php /*
<div class="row">
    <div class="col-sm-6">
        <?php
        $hours_id = isset(array_flip($aHours)[$hours]) ? array_flip($aHours)[$hours] : 0;
        echo Html::dropDownList($attribute_name.'_hour', $hours_id, $aHours, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto;']);
        ?>
    </div>
    <div class="col-sm-6">
        <?php
        $minutes_id = isset(array_flip($aMinutes)[$minutes]) ? array_flip($aMinutes)[$minutes] : 0;
        echo Html::dropDownList($attribute_name.'_minute', $minutes_id, $aMinutes, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto;']);
        ?>
    </div>
</div>
 */ ?>
<?php
$hours_id = isset(array_flip($aHours)[$hours]) ? array_flip($aHours)[$hours] : 0;
echo Html::dropDownList($attribute_name.'_hour', $hours_id, $aHours, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto; margin-right:10px;']);

$minutes_id = isset(array_flip($aMinutes)[$minutes]) ? array_flip($aMinutes)[$minutes] : 0;
echo Html::dropDownList($attribute_name.'_minute', $minutes_id, $aMinutes, ['class' => 'form-control', 'style'=>'display: inline-block; width: auto;']);