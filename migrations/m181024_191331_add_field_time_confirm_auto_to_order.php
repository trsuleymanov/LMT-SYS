<?php

use yii\db\Migration;

/**
 * Class m181024_191331_add_field_time_confirm_auto_to_order
 */
class m181024_191331_add_field_time_confirm_auto_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'time_confirm_auto', $this->integer()->comment('Автоматическое ВРПТ')->after('time_confirm'));
    }

    public function down()
    {
        $this->dropColumn('order', 'time_confirm_auto');
    }
}
