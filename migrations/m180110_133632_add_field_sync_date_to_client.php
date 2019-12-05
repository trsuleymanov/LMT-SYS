<?php

use yii\db\Migration;

class m180110_133632_add_field_sync_date_to_client extends Migration
{
    public function up()
    {
        $this->addColumn('client', 'sync_date', $this->integer()->comment('Дата синхронизации с клиентским сервером'));
    }

    public function down()
    {
        $this->dropColumn('client', 'sync_date');
    }
}
