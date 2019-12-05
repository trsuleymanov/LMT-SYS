<?php

use yii\db\Migration;

/**
 * Class m180625_115652_add_field_without_transport_to_storage_operation
 */
class m180625_115652_add_field_without_transport_to_storage_operation extends Migration
{
    public function up()
    {
        $this->addColumn('storage_operation', 'without_transport', $this->boolean()->defaultValue(false)->after('count')->comment('без участия тс'));
    }

    public function down()
    {
        $this->dropColumn('storage_operation', 'without_transport');
    }
}
