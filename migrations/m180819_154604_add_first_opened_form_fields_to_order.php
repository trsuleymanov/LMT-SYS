<?php

use yii\db\Migration;

/**
 * Class m180819_154604_add_first_opened_form_fields_to_order
 */
class m180819_154604_add_first_opened_form_fields_to_order extends Migration
{
    public function up()
    {
        $this->addColumn('order', 'first_opened_form_time', $this->integer()->after('is_confirmed')->comment('Время первого открытия формы заказа'));
        $this->addColumn('order', 'first_opened_form_user_id', $this->integer()->after('last_writedown_click_time')->comment('Пользователь первым открывший форму заказа'));
    }

    public function down()
    {
        $this->dropColumn('order', 'first_opened_form_time');
        $this->dropColumn('order', 'first_opened_form_user_id');
    }
}
