<?php

use yii\db\Migration;

/**
 * Class m190624_193243_add_field_sync_date_to_setting
 */
class m190624_193243_add_field_sync_date_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'sync_date', $this->integer()->defaultValue(0)->comment('Время когда была синхронизация с клиентским сайтом'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'sync_date');
    }
}
