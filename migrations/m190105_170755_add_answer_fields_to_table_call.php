<?php

use yii\db\Migration;

/**
 * Class m190105_170755_add_answer_fields_to_table_call
 */
class m190105_170755_add_answer_fields_to_table_call extends Migration
{
    public function up()
    {
        $this->addColumn('call', 'answered_at',  $this->integer()->comment('Начало разговора')->after('created_at'));
        $this->addColumn('call', 'ats_answer_time',  $this->bigInteger()->comment('Начало разговора (по версии АТС)')->after('ats_start_time'));
    }

    public function down()
    {
        $this->dropColumn('call', 'answered_at');
        $this->dropColumn('call', 'ats_answer_time');
    }
}
