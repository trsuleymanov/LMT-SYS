<?php

use yii\db\Migration;

/**
 * Class m180527_131416_add_field_storage_id_to_table_storage_operation
 */
class m180527_131416_add_field_storage_id_to_table_storage_operation extends Migration
{
    public function up()
    {
        $this->addColumn('storage_operation', 'storage_id', $this->integer()->after('id')->comment('Склад'));
    }

    public function down()
    {
        $this->dropColumn('storage_operation', 'storage_id');
    }
}
