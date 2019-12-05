<?php

use yii\db\Migration;

/**
 * Class m190207_172614_add_field_caused_by_missed_call_window_to_call
 */
class m190207_172614_add_field_caused_by_missed_call_window_to_call extends Migration
{
    public function up()
    {
        $this->addColumn('call', 'caused_by_missed_call_window',  $this->boolean()->defaultValue(0)->comment('Звонок был вызван из окна пропущенных звонков')->after('call_direction'));
    }

    public function down()
    {
        $this->dropColumn('call', 'caused_by_missed_call_window');
    }
}
