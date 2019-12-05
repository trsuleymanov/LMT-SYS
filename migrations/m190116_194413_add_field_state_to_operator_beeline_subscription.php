<?php

use yii\db\Migration;

/**
 * Class m190116_194413_add_field_state_to_operator_beeline_subscription
 */
class m190116_194413_add_field_state_to_operator_beeline_subscription extends Migration
{
    public function up()
    {
        $this->addColumn('operator_beeline_subscription', 'status',  $this->string(20)->defaultValue("ONLINE")->comment('Статус')->after('operator_id'));
    }

    public function down()
    {
        $this->dropColumn('operator_beeline_subscription', 'status');
    }
}
