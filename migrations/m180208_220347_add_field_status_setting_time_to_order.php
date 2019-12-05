<?php

use yii\db\Migration;

/**
 * Class m180208_220347_add_field_status_setting_time_to_order
 */
class m180208_220347_add_field_status_setting_time_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'status_setting_time', $this->integer()->comment('Время установки статуса')->after('status_id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'status_setting_time');
    }
}
