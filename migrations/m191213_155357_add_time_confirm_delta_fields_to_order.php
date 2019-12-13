<?php

use yii\db\Migration;

/**
 * Class m191213_155357_add_time_confirm_delta_fields_to_order
 */
class m191213_155357_add_time_confirm_delta_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'time_confirm_diff', $this->integer()->comment('Разница между прежним ВРПТ и временем изменения/объединения рейса')->after('time_confirm_sort'));
        $this->addColumn('order', 'time_confirm_delta', $this->integer()->defaultValue(0)->comment('Разница ВРПТ прежнего и нового, сек')->after('time_confirm_diff'));
    }

    public function down()
    {
        $this->dropColumn('order', 'time_confirm_diff');
        $this->dropColumn('order', 'time_confirm_delta');
    }
}
