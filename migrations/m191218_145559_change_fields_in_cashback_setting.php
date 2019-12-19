<?php

use yii\db\Migration;

/**
 * Class m191218_145559_change_fields_in_cashback_setting
 */
class m191218_145559_change_fields_in_cashback_setting extends Migration
{
    public function up()
    {
        $this->dropColumn('cashback_setting', 'order_penalty_percent');
        $this->dropColumn('cashback_setting', 'hours_before_start_trip_for_penalty');

        $this->addColumn('cashback_setting', 'red_penalty_max_time', $this->smallInteger()->defaultValue(1800)->comment('Максимальное время красной зоны')->after('order_accrual_percent'));
        $this->addColumn('cashback_setting', 'order_red_penalty_percent', $this->smallInteger()->defaultValue(0)->comment('Процент штрафа от стоимости заказа для красной зоны')->after('red_penalty_max_time'));

        $this->addColumn('cashback_setting', 'yellow_penalty_max_time', $this->smallInteger()->defaultValue(3600)->comment('Максимальное время желтой зоны')->after('order_red_penalty_percent'));
        $this->addColumn('cashback_setting', 'order_yellow_penalty_percent', $this->smallInteger()->defaultValue(0)->comment('Процент штрафа от стоимости заказа для желтой зоны')->after('yellow_penalty_max_time'));

        $this->addColumn('cashback_setting', 'max_time_confirm_diff', $this->smallInteger()->defaultValue(0)->comment('Максимальное время разницы между ВРПТ при которой штрафные зоны работают')->after('order_yellow_penalty_percent'));
        $this->addColumn('cashback_setting', 'max_time_confirm_delta', $this->smallInteger()->defaultValue(0)->comment('Максимальное время разницы между прежним ВРПТ и временем изменения/объединения рейса при которой штрафные зоны работают')->after('max_time_confirm_diff'));
    }

    public function down()
    {
        $this->addColumn('cashback_setting', 'order_penalty_percent', $this->smallInteger()->defaultValue(0)->comment('Процент штафа с заказа')->after('order_accrual_percent'));
        $this->addColumn('cashback_setting', 'hours_before_start_trip_for_penalty', $this->smallInteger()->defaultValue(0)->comment('Часы до начала рейса являющиеся условием начисления штрафа')->after('order_penalty_percent'));

        $this->dropColumn('cashback_setting', 'red_penalty_max_time');
        $this->dropColumn('cashback_setting', 'order_red_penalty_percent');
        $this->dropColumn('cashback_setting', 'yellow_penalty_max_time');
        $this->dropColumn('cashback_setting', 'order_yellow_penalty_percent');
        $this->dropColumn('cashback_setting', 'max_time_confirm_diff');
        $this->dropColumn('cashback_setting', 'max_time_confirm_delta');
    }
}
