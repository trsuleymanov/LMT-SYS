<?php

use app\components\Helper;
use app\models\Setting;
use yii\helpers\ArrayHelper;

if(count($missed_cases) > 0) {
    $aActiveCallsOperands = ArrayHelper::map($active_calls, 'operand', 'operand');

    ?>
    <div id="missed-call-list">
        <?php
        foreach ($missed_cases as $case) { ?>
            <div class="missed-call<?= (isset($aActiveCallsOperands[$case->operand]) ? ' disable' : '') ?>" case-id="<?= $case->id ?>" operand="<?= $case->operand ?>">
                <?= Setting::changeShowingPhone($case->operand, 'show_short_clients_phones') ?>&nbsp;&nbsp;<?= (isset($aClientsPhone[$case->operand]) ? $aClientsPhone[$case->operand]->name : '') ?>&nbsp;&nbsp;<?= date('d.m.Y H:i', $case->open_time)?>&nbsp;&nbsp;(<?= ($case->call_count.' '.Helper::getNumberString($case->call_count, 'звонок', 'звонка', 'звонков')) ?>)&nbsp;&nbsp;<button class="call-phone-button" phone="<?= $case->operand ?>"><small><i class="glyphicon glyphicon-earphone"></i></small> Обработать</button>
            </div>
        <?php } ?>
    </div>
<?php } ?>


