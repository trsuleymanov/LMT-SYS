<?php

use yii\db\Migration;

/**
 * Class m181020_111031_add_sync_fields_to_yandex_point
 */
class m181020_111031_add_sync_fields_to_yandex_point extends Migration
{
    public function up()
    {
        $this->addColumn('yandex_point', 'external_use', $this->boolean()->defaultValue(true)->comment('Внешнее использование (да/нет)')->after('id'));
        $this->addColumn('yandex_point', 'sync_date', $this->integer()->comment('Дата синхронизации с клиенским сервером'));
    }

    public function down()
    {
        $this->dropColumn('yandex_point', 'sync_date');
        $this->dropColumn('yandex_point', 'external_use');
    }
}
