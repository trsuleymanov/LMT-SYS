<?php

use yii\db\Migration;

/**
 * Class m190205_130705_delete_field_call_appeal_id_from_dispatcher_accounting
 */
class m190205_130705_delete_field_call_appeal_id_from_dispatcher_accounting extends Migration
{
    public function up()
    {
        $this->dropColumn('dispatcher_accounting', 'call_appeal_id');
    }

    public function down()
    {
        $this->addColumn('dispatcher_accounting', 'call_appeal_id', $this->integer()->after('id')->comment('Обращение'));
    }
}
