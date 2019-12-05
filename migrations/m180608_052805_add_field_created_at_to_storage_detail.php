<?php

use yii\db\Migration;

/**
 * Class m180608_052805_add_field_created_at_to_storage_detail
 */
class m180608_052805_add_field_created_at_to_storage_detail extends Migration
{
    public function up()
    {
        $this->addColumn('storage_detail', 'created_at', $this->integer()->after('comment')->comment('Время создания'));
    }

    public function down()
    {
        $this->dropColumn('storage_detail', 'created_at');
    }
}
