<?php

use yii\db\Migration;

/**
 * Class m180629_213733_add_field_device_code_to_driver
 */
class m180629_213733_add_field_device_code_to_driver extends Migration
{
    public function up()
    {
        $this->addColumn('driver', 'device_code', $this->string(17)->after('secondary_transport_id')->comment('Уникальный код мобильного устройства'));
    }

    public function down()
    {
        $this->dropColumn('driver', 'device_code');
    }
}
