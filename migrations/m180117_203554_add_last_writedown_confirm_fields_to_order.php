<?php

use yii\db\Migration;

class m180117_203554_add_last_writedown_confirm_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'last_writedown_click_time', $this->integer()->comment('Время последнего нажатия кнопки Записать')->after('first_writedown_click_time'));
        $this->addColumn('order', 'last_writedown_clicker_id', $this->integer()->comment('Пользователь (диспетчер) последний нажавший кнопку Записать')->after('first_writedown_clicker_id'));
        $this->addColumn('order', 'last_confirm_click_time', $this->integer()->comment('Время последнего нажатия кнопки Подтвердить')->after('first_confirm_click_time'));
        $this->addColumn('order', 'last_confirm_clicker_id', $this->integer()->comment('Пользователь (диспетчер) последний нажавший кнопку Подтвердить')->after('first_confirm_clicker_id'));
    }

    public function down()
    {
        $this->dropColumn('order', 'last_writedown_click_time');
        $this->dropColumn('order', 'last_writedown_clicker_id');
        $this->dropColumn('order', 'last_confirm_click_time');
        $this->dropColumn('order', 'last_confirm_clicker_id');
    }
}
