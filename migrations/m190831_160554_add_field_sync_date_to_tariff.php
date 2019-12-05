<?php

use yii\db\Migration;

/**
 * Class m190831_160554_add_field_sync_date_to_tariff
 */
class m190831_160554_add_field_sync_date_to_tariff extends Migration
{
    public function up()
    {
        $this->addColumn('tariff', 'sync_date', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('tariff', 'sync_date');
    }
}
