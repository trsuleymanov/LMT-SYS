<?php

use yii\db\Migration;

/**
 * Class m191210_224035_add_field_extended_external_use_to_city
 */
class m191210_224035_add_field_extended_external_use_to_city extends Migration
{
    public function up()
    {
        $this->addColumn('city', 'extended_external_use', $this->boolean()->defaultValue(false)->comment('Расширенное внешнее использование')->after('name'));
        $this->addColumn('city', 'sync_date', $this->integer()->comment('Дата синхронизации с клиентским сервером'));
    }

    public function down()
    {
        $this->dropColumn('city', 'extended_external_use');
        $this->dropColumn('city', 'sync_date');
    }
}
