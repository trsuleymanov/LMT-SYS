<?php

use yii\db\Migration;


class m180212_152623_add_field_active_to_driver extends Migration
{
    public function up()
    {
        $this->addColumn('driver', 'active', $this->boolean()->defaultValue(true)->comment('Активен')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('driver', 'active');
    }
}
