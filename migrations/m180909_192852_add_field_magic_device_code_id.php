<?php

use yii\db\Migration;

/**
 * Class m180909_192852_add_field_magic_device_code_id
 */
class m180909_192852_add_field_magic_device_code_id extends Migration
{
    public function up()
    {
        $this->addColumn('driver', 'magic_device_code_id', $this->integer()->after('device_code')->comment('Магический код мобильного устройства'));
    }

    public function down()
    {
        $this->dropColumn('driver', 'magic_device_code_id');
    }
}
