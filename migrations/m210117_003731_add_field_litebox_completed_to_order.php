<?php

use yii\db\Migration;

/**
 * Class m210117_003731_add_field_litebox_completed_to_order
 */
class m210117_003731_add_field_litebox_completed_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'litebox_completed', $this->boolean()->defaultValue(0)->after('payment_source'));
    }

    public function down()
    {
        $this->dropColumn('order', 'litebox_completed');
    }
}
