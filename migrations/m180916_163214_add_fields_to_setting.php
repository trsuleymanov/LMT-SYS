<?php

use yii\db\Migration;

/**
 * Class m180916_163214_add_fields_to_setting
 */
class m180916_163214_add_fields_to_setting extends Migration
{
    public function up()
    {
        $this->addColumn('setting', 'show_short_clients_phones', $this->boolean()->defaultValue(false)->comment('Отображать номера клиентов в коротком формате'));
        $this->addColumn('setting', 'show_short_drivers_phones', $this->boolean()->defaultValue(false)->comment('Отображать номера водителей в коротком формате'));
        $this->addColumn('setting', 'access_to_client_info_main_page', $this->boolean()->defaultValue(true)->comment('Доступ к инф.о клиенте через меню поиска в главном окне'));
    }

    public function down()
    {
        $this->dropColumn('setting', 'show_short_clients_phones');
        $this->dropColumn('setting', 'show_short_drivers_phones');
        $this->dropColumn('setting', 'access_to_client_info_main_page');
    }
}
