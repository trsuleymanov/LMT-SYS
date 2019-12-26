<?php

use yii\db\Migration;

/**
 * Class m191226_144219_add_field_payment_source_to_order
 */
class m191226_144219_add_field_payment_source_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'payment_source', "ENUM('client_site', 'application', 'crm', '') DEFAULT '' AFTER paid_time");
    }

    public function down()
    {
        $this->dropColumn('order', 'payment_source');
    }
}
