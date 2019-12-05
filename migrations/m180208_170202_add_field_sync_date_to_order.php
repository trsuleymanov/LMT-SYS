<?php

use yii\db\Migration;

/**
 * Class m180208_170202_add_field_sync_date_to_order
 */
class m180208_170202_add_field_sync_date_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'client_server_ext_id', $this->integer()->comment('id заявки на клиентском сервере ')->after('client_ext_id'));
        $this->addColumn('order', 'sync_date', $this->integer()->comment('Дата синхронизации с клиенским сервером')->after('client_server_ext_id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'client_server_ext_id');
        $this->dropColumn('order', 'sync_date');
    }
}
