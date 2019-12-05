<?php

use yii\db\Migration;

/**
 * Class m180203_151206_delete_field_sync_date_from_client
 */
class m180203_151206_delete_field_sync_date_from_client extends Migration
{
    public function up()
    {
        $this->dropColumn('client', 'sync_date');
    }

    public function down()
    {
        $this->addColumn('client', 'sync_date', $this->integer()->comment('Дата синхронизации с клиентским сервером'));
    }
}
