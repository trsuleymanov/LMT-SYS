<?php

use yii\db\Migration;

/**
 * Class m190528_182646_add_field_sync_date_to_client
 */
class m190528_182646_add_field_sync_date_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'sync_date', $this->integer()->comment('Дата синхронизации с клиенским сервером'));
    }

    public function down()
    {
        $this->dropColumn('client', 'sync_date');
    }
}
