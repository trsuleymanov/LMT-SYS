<?php

use yii\db\Migration;

/**
 * Class m181229_134954_add_fields_to_call_tables
 */
class m181229_134954_add_fields_to_call_tables extends Migration
{
    public function up()
    {
        $this->addColumn('call', 'finished_at',  $this->integer()->comment('Окончание звонка')->after('created_at'));
        $this->addColumn('call_event', 'created_at',  $this->integer()->comment('Создан')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('call', 'finished_at');
        $this->dropColumn('call_event', 'created_at');
    }
}
