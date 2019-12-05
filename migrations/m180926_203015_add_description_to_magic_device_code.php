<?php

use yii\db\Migration;

class m180926_203015_add_description_to_magic_device_code extends Migration
{
    public function up()
    {
        $this->addColumn('magic_device_code', 'description', $this->text()->comment('Описание'));
    }

    public function down()
    {
        $this->dropColumn('magic_device_code', 'description');
    }
}
