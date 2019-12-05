<?php

use yii\db\Migration;

/**
 * Class m180623_155439_add_fields_to_storage_operation
 */
class m180623_155439_add_fields_to_storage_operation extends Migration
{
    public function up()
    {
        $this->addColumn('storage_operation', 'creator_id', $this->integer()->after('driver_id')->comment('Пользователь, создавший операцию'));
        $this->addColumn('storage_operation', 'date', $this->integer()->after('id')->comment('Дата операции'));
    }

    public function down()
    {
        $this->dropColumn('storage_operation', 'creator_id');
        $this->dropColumn('storage_operation', 'date');
    }
}
