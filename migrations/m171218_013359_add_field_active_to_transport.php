<?php

use yii\db\Migration;

class m171218_013359_add_field_active_to_transport extends Migration
{
    public function up()
    {
        $this->addColumn('transport', 'active', $this->boolean()->defaultValue(true)->comment('Т/с активно')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('transport', 'active');
    }
}
