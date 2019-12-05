<?php

use yii\db\Migration;

class m171013_154312_add_cancellation_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'cancellation_click_time', $this->integer()->comment('Время отмены')->after('cancellation_reason_id'));
        $this->addColumn('order', 'cancellation_clicker_id', $this->integer()->comment('Пользователь совершивший отмену')->after('cancellation_click_time'));
    }

    public function down()
    {
        $this->dropColumn('order', 'cancellation_click_time');
        $this->dropColumn('order', 'cancellation_clicker_id');
    }
}
