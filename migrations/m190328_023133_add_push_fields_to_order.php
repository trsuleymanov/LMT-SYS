<?php

use yii\db\Migration;

/**
 * Class m190328_023133_add_push_fields_to_order
 */
class m190328_023133_add_push_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'push_send_time', $this->integer()->comment('Время отправки пуша')->after('radio_group_3'));
        $this->addColumn('order', 'push_confirm_time', $this->integer()->comment('Время подтверждения пуша')->after('push_send_time'));
        $this->addColumn('order', 'push_rejection_time', $this->integer()->comment('Время отказа пуша')->after('push_confirm_time'));
    }

    public function down()
    {
        $this->dropColumn('order', 'push_send_time');
        $this->dropColumn('order', 'push_confirm_time');
        $this->dropColumn('order', 'push_rejection_time');
    }
}
