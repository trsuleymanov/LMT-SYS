<?php

use yii\db\Migration;

/**
 * Class m190215_190051_add_field_ats_eventID_to_call_event
 */
class m190215_190051_add_field_ats_eventID_to_call_event extends Migration
{
    public function up()
    {
        $this->truncateTable('call');
        $this->truncateTable('call_event');
        $this->truncateTable('call_case');
        $this->truncateTable('call_docking');

        $this->addColumn('call_event', 'ats_eventID', $this->string(36)->comment('События id в АТС')->after('id'));
    }

    public function down()
    {
        $this->dropColumn('call_event', 'ats_eventID');
    }
}
