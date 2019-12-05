<?php

use yii\db\Migration;

class m170622_171411_order_add_field_radio_confirm_now extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order', 'radio_confirm_now', $this->smallInteger()->comment('Группа radio-кнопок "Подтвердить сейчас" / "Не подтверждать"')->after('first_confirm_clicker_id'));
    }

    public function safeDown()
    {
        $this->dropColumn('order', 'radio_confirm_now');
    }
}
