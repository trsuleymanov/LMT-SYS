<?php

use yii\db\Migration;

class m171228_190345_add_field_to_dispatcher_accounting extends Migration
{
    public function up()
    {
        $this->addColumn('dispatcher_accounting', 'value', $this->string(40)->defaultValue('')->comment('Доп.поле'));
    }

    public function down()
    {
        $this->dropColumn('dispatcher_accounting', 'value');
    }
}
