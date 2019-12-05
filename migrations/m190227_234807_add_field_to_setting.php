<?php

use yii\db\Migration;

/**
 * Class m190227_234807_add_field_to_setting
 */
class m190227_234807_add_field_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'missed_calls_close_interval', $this->integer()->defaultValue(18000)->comment('Количество секунд до закрытия пропущенных обращений'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'missed_calls_close_interval');
    }
}
