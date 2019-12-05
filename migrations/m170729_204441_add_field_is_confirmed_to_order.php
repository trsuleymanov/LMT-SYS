<?php

use yii\db\Migration;

class m170729_204441_add_field_is_confirmed_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'is_confirmed', $this->boolean()->defaultValue(0)->after('time_confirm')->comment('Подтвержден'));
    }

    public function down()
    {
        $this->dropColumn('order', 'is_confirmed');
    }
}
